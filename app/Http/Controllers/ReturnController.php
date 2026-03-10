<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    private function userHasRole(string $role): bool
    {
        $user = Auth::user();

        if (!$user || !method_exists($user, 'hasRole')) {
            return false;
        }

        return (bool) call_user_func([$user, 'hasRole'], $role);
    }

    public function index(Request $request)
    {
        $query = ReturnModel::with(['sale.customer', 'creator'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                    ->orWhereHas('sale', function ($saleQuery) use ($search) {
                        $saleQuery->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        $returns = $query->latest('return_date')->paginate(20);

        $stats = [
            'total_returns' => ReturnModel::where('status', 'completed')->sum('total_amount'),
            'pending_count' => ReturnModel::where('status', 'pending')->count(),
            'today_returns' => ReturnModel::whereDate('return_date', today())->count(),
        ];

        return view('returns.index', compact('returns', 'stats'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->filled('sale_id')) {
            $sale = Sale::with(['customer', 'items.product.unit'])
                ->findOrFail($request->sale_id);
        }

        return view('returns.create', compact('sale'));
    }

    public function searchSale(Request $request)
    {
        $invoice = $request->input('invoice');
        if ($invoice) {
            $sale = Sale::with(['customer', 'items.product'])
                ->where('status', 'completed')
                ->where('invoice_number', $invoice)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale not found',
                ]);
            }

            return response()->json([
                'success' => true,
                'sale' => $sale,
            ]);
        }

        $search = $request->input('q', '');

        $sales = Sale::with(['customer', 'items.product'])
            ->where('status', 'completed')
            ->where(function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => $sale->customer?->name ?? 'Walk-in Customer',
                    'total_amount' => $sale->total_amount,
                    'created_at' => $sale->created_at->format('Y-m-d'),
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->total,
                        ];
                    }),
                ];
            });

        return response()->json($sales);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'refund_method' => 'required|in:cash,card,store_credit',
            'reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Validate return quantities against original sale
            $sale = Sale::with('items')->findOrFail($validated['sale_id']);

            $selectedItems = [];
            foreach ($validated['items'] as $saleItemId => $returnItem) {
                if (empty($returnItem['selected'])) {
                    continue;
                }

                $quantity = (float) ($returnItem['quantity'] ?? 0);
                if ($quantity <= 0) {
                    continue;
                }

                $saleItem = $sale->items->firstWhere('id', (int) $saleItemId);
                if (!$saleItem) {
                    throw new \Exception('Invalid sale item selected.');
                }

                $alreadyReturned = ReturnItem::whereHas('return', function ($query) use ($validated) {
                    $query->where('sale_id', $validated['sale_id'])
                        ->where('status', 'completed');
                })->where('sale_item_id', $saleItem->id)
                    ->sum('quantity');

                if (($alreadyReturned + $quantity) > $saleItem->quantity) {
                    throw new \Exception("Return quantity exceeds available quantity for {$saleItem->product_name}.");
                }

                $selectedItems[] = [
                    'sale_item' => $saleItem,
                    'quantity' => $quantity,
                ];
            }

            if (count($selectedItems) === 0) {
                throw new \Exception('Please select at least one item with quantity to return.');
            }

            $subtotal = collect($selectedItems)->sum(function ($item) {
                return $item['quantity'] * (float) $item['sale_item']->unit_price;
            });

            $taxAmount = collect($selectedItems)->sum(function ($item) {
                $saleItem = $item['sale_item'];
                if ((float) $saleItem->quantity <= 0) {
                    return 0;
                }
                $taxPerUnit = (float) $saleItem->tax_amount / (float) $saleItem->quantity;
                return $taxPerUnit * $item['quantity'];
            });

            $totalAmount = $subtotal + $taxAmount;

            // Create return
            $return = ReturnModel::create([
                'sale_id' => $validated['sale_id'],
                'customer_id' => $sale->customer_id,
                'return_date' => $validated['return_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'refund_amount' => $totalAmount,
                'refund_method' => $validated['refund_method'],
                'status' => 'pending',
                'reason' => $validated['reason'],
                'created_by' => Auth::id(),
            ]);

            // Create return items
            foreach ($selectedItems as $item) {
                $saleItem = $item['sale_item'];
                $quantity = $item['quantity'];

                $taxPerUnit = (float) $saleItem->quantity > 0
                    ? (float) $saleItem->tax_amount / (float) $saleItem->quantity
                    : 0;

                ReturnItem::create([
                    'return_id' => $return->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'product_name' => $saleItem->product_name,
                    'quantity' => $quantity,
                    'unit_price' => $saleItem->unit_price,
                    'tax_amount' => $taxPerUnit * $quantity,
                    'total' => ($saleItem->unit_price * $quantity) + ($taxPerUnit * $quantity),
                    'reason' => $validated['reason'],
                ]);
            }

            DB::commit();

            return redirect()->route('returns.show', $return)
                ->with('success', 'Return created successfully. Please complete the return to update stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create return: ' . $e->getMessage()]);
        }
    }

    public function show(ReturnModel $return)
    {
        $return->load(['sale.customer', 'items.product.unit', 'creator']);
        return view('returns.show', compact('return'));
    }

    public function edit(ReturnModel $return)
    {
        if ($return->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending returns can be edited.']);
        }

        $return->load(['sale.customer', 'items.product']);
        return view('returns.edit', compact('return'));
    }

    public function update(Request $request, ReturnModel $return)
    {
        if ($return->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending returns can be updated.']);
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'refund_method' => 'required|in:cash,card,store_credit',
            'reason' => 'required|string',
        ]);

        $return->update([
            'return_date' => $validated['return_date'],
            'refund_method' => $validated['refund_method'],
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('returns.show', $return)
            ->with('success', 'Return updated successfully.');
    }

    public function complete(ReturnModel $return)
    {
        if ($return->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending returns can be completed.']);
        }

        DB::beginTransaction();
        try {
            // Update product stocks
            foreach ($return->items as $item) {
                $product = $item->product;
                $stock = $product->stock ?? Stock::create([
                    'product_id' => $product->id,
                    'quantity' => 0,
                    'last_updated_by' => Auth::id(),
                ]);

                $previousQuantity = $stock->quantity;
                $newQuantity = $previousQuantity + $item->quantity;

                $stock->update([
                    'quantity' => $newQuantity,
                    'last_updated_by' => Auth::id(),
                ]);

                // Log stock movement
                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'previous_quantity' => $previousQuantity,
                    'current_quantity' => $newQuantity,
                    'reference_type' => 'return',
                    'reference_id' => $return->id,
                    'notes' => "Return: {$return->return_number}",
                    'created_by' => Auth::id(),
                ]);
            }

            $return->update([
                'status' => 'completed',
            ]);

            DB::commit();

            return back()->with('success', 'Return completed and stock updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to complete return: ' . $e->getMessage()]);
        }
    }

    public function cancel(Request $request, ReturnModel $return)
    {
        if ($return->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot cancel completed returns.']);
        }

        $return->update([
            'status' => 'rejected',
            'reason' => trim(($return->reason ?? '') . "\n" . $request->input('cancellation_reason', '')),
        ]);

        return back()->with('success', 'Return cancelled.');
    }

    public function destroy(ReturnModel $return)
    {
        // Only admin can delete, and only pending/cancelled returns
        if (!$this->userHasRole('admin')) {
            abort(403, 'Only administrators can delete returns.');
        }

        if ($return->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot delete completed returns.']);
        }

        $return->delete();

        return redirect()->route('returns.index')
            ->with('success', 'Return deleted successfully.');
    }
}
