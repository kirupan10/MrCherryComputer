<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReturnSale;
use App\Models\ReturnSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnSaleController extends Controller
{
    /**
     * Display a listing of returns.
     */
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id ?? null;

        $base = ReturnSale::query();
        if ($shopId) {
            $base->where('shop_id', $shopId);
        }

        // Get all returns ordered by date descending
        $allReturns = $base->with(['customer', 'order', 'items.product'])
            ->orderBy('return_date', 'desc')
            ->get();

        // Group by month-year
        $returnsByMonth = $allReturns->groupBy(function($return) {
            return $return->return_date ? $return->return_date->format('F Y') : 'No Date';
        });

        // Calculate totals
        $totalReturns = $allReturns->sum('total');
        $totalRecords = $allReturns->count();
        $totalItems = $allReturns->sum(function($return) {
            return $return->items->sum('quantity');
        });

        return view('returns.index', [
            'returnsByMonth' => $returnsByMonth,
            'totalReturns' => $totalReturns,
            'totalRecords' => $totalRecords,
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * Display a specific return.
     */
    public function show(ReturnSale $returnSale)
    {
        $returnSale->load(['customer', 'order', 'items.product', 'createdBy']);
        return view('returns.show', compact('returnSale'));
    }

    /**
     * Delete a return sale.
     */
    public function destroy(ReturnSale $returnSale)
    {
        $returnSale->delete();
        return redirect()->route('returns.index')->with('success', 'Return deleted successfully');
    }

    /**
     * Store a return sale and adjust stock quantities.
     * Expected payload:
     * - order_id (optional)
     * - customer_id (optional)
     * - return_date (optional)
     * - notes (optional)
     * - items: [ { product_id, quantity, serial_number? } ]
     */
    public function store(Request $request)
    {
        // Detect shop type to use the correct product model and table
        $shop = $request->user()->getActiveShop();
        $isTechShop = $shop && $shop->shop_type && $shop->shop_type->value === 'tech';
        $productTable = $isTechShop
            ? (new \App\ShopTypes\Tech\Models\TechProduct)->getTable()
            : 'products';

        $data = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'customer_id' => 'nullable|exists:customers,id',
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => "required|exists:{$productTable},id",
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.serial_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $subTotal = 0;
            $total = 0;

            $returnSale = ReturnSale::create([
                'order_id' => $data['order_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'return_date' => $data['return_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'shop_id' => $request->user()->shop_id ?? null,
                'created_by' => $request->user()->id ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $product = $isTechShop
                    ? \App\ShopTypes\Tech\Models\TechProduct::findOrFail($item['product_id'])
                    : Product::findOrFail($item['product_id']);
                $qty = (int)$item['quantity'];

                // Determine unit cost from product selling price (prices stored as plain integers, not cents)
                $unitcost = (int)round($product->selling_price);
                $lineTotal = $unitcost * $qty;

                // Create return item
                $serialNumber = $item['serial_number'] ?? null;
                ReturnSaleItem::create([
                    'return_sale_id' => $returnSale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unitcost' => $unitcost,
                    'total' => $lineTotal,
                    'serial_number' => $serialNumber ? strtoupper($serialNumber) : null,
                ]);

                // Tech products are stored in a different table, so increment stock directly here.
                if ($isTechShop) {
                    $product->quantity = round(((float)($product->quantity ?? 0)) + $qty, 3);
                    $product->save();
                }

                $subTotal += $lineTotal;
                $total += $lineTotal; // future: taxes/fees
            }

            $returnSale->sub_total = $subTotal;
            $returnSale->total = $total;
            $returnSale->save();

            // Keep existing stock adjustment flow for non-tech shops.
            if (!$isTechShop) {
                $stockService = app(\App\Services\StockService::class);
                $stockService->adjustStockAfterReturn($returnSale->id);
            }

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'ok', 'return_sale_id' => $returnSale->id], 201);
            }

            return redirect()
                ->to(shop_route('returns.edit', $returnSale))
                ->with('success', 'Return recorded successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to record return', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show create return form (simple POS-like selection).
     */
    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id ?? null;

        // Detect shop type to use the correct product model
        $shop = $request->user()->getActiveShop();
        $isTechShop = $shop && $shop->shop_type && $shop->shop_type->value === 'tech';

        // Products for the selection
        if ($isTechShop) {
            $products = \App\ShopTypes\Tech\Models\TechProduct::where('shop_id', $shopId)
                ->orderBy('name')->get();
        } else {
            $products = Product::orderBy('name')->get();
        }

        // Customers for the dropdown
        $customers = \App\Models\Customer::where('shop_id', $shopId)->orderBy('name')->get();

        // Base query for returns scoped to shop
        $base = ReturnSale::query();
        if ($shopId) {
            $base->where('shop_id', $shopId);
        }

    // KPIs - use DB-side stored procedure via KpiService for faster reads
    $kpiService = new \App\Services\KpiService();
    $returnKpis = $kpiService->getReturnKpisByShop($shopId);

    $totalReturns = $returnKpis->total_returns ?? 0; // in cents
    // last_30_days_total as a proxied month/period number
    $monthTotal = $returnKpis->last_30_days_total ?? 0;
    $weekTotal = 0; // week-level cached proc not available; keep 0 or compute if necessary

        // Items returned (sum of quantities) scoped to shop
        $itemsQuery = ReturnSaleItem::query();
        if ($shopId) {
            $itemsQuery->whereHas('returnSale', function ($q) use ($shopId) {
                $q->where('shop_id', $shopId);
            });
        }
    $itemsReturned = $returnKpis->items_returned ?? $itemsQuery->sum('quantity');

        // Recent returns
        $recent = (clone $base)->latest('return_date')->latest()->limit(10)->with('items.product')->get();

        return view('returns.create', [
            'products' => $products,
            'customers' => $customers,
            'totalReturns' => $totalReturns,
            'monthTotal' => $monthTotal,
            'weekTotal' => $weekTotal,
            'itemsReturned' => $itemsReturned,
            'recentReturns' => $recent,
        ]);
    }

    /**
     * Show edit form for a return sale. (Edit metadata only)
     */
    public function edit(ReturnSale $returnSale)
    {
        // Load items for display
        $returnSale->load('items.product');
        return view('returns.edit', compact('returnSale'));
    }

    /**
     * Update return sale metadata (notes, date). Does not alter item quantities.
     */
    public function update(Request $request, ReturnSale $returnSale)
    {
        $data = $request->validate([
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $returnSale->update([
            'return_date' => $data['return_date'] ?? $returnSale->return_date,
            'notes' => $data['notes'] ?? $returnSale->notes,
        ]);

        return redirect()->route('returns.edit', $returnSale)->with('status', 'Return updated');
    }
}
