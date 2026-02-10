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
    public function index(Request $request)
    {
        $query = ReturnModel::with(['sale.customer', 'processedBy'])
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
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'refund_method' => 'required|in:cash,card,upi,bank_transfer,store_credit',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Validate return quantities against original sale
            $sale = Sale::with('items')->findOrFail($validated['sale_id']);

            foreach ($validated['items'] as $returnItem) {
                $saleItem = $sale->items->firstWhere('product_id', $returnItem['product_id']);
                if (!$saleItem) {
                    throw new \Exception('Product not found in original sale.');
                }

                // Check already returned quantity
                $alreadyReturned = ReturnItem::whereHas('return', function ($query) use ($validated) {
                    $query->where('sale_id', $validated['sale_id'])
                        ->where('status', 'completed');
                })->where('product_id', $returnItem['product_id'])
                  ->sum('quantity');

                if (($alreadyReturned + $returnItem['quantity']) > $saleItem->quantity) {
                    $product = Product::find($returnItem['product_id']);
                    throw new \Exception("Return quantity exceeds available quantity for {$product->name}.");
                }
            }

            // Create return
            $return = ReturnModel::create([
                'sale_id' => $validated['sale_id'],
                'return_date' => $validated['return_date'],
                'total_amount' => $validated['total_amount'],
                'refund_method' => $validated['refund_method'],
                'status' => 'pending',
                'notes' => $validated['notes'],
                'processed_by' => Auth::id(),
            ]);

            // Create return items
            foreach ($validated['items'] as $item) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                    'reason' => $item['reason'] ?? null,
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
        $return->load(['sale.customer', 'items.product.unit', 'processedBy']);
        return view('returns.show', compact('return'));
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
                'completed_at' => now(),
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
            'status' => 'cancelled',
            'notes' => $request->input('cancellation_reason', '') . "\n" . $return->notes,
        ]);

        return back()->with('success', 'Return cancelled.');
    }

    public function destroy(ReturnModel $return)
    {
        // Only admin can delete, and only pending/cancelled returns
        if (!Auth::user()->hasRole('admin')) {
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
