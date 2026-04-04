<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function normalizeDateRange(Request $request, int $days = 30): array
    {
        $dateFrom = $request->input('date_from', now()->subDays($days)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $request->merge([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return [$dateFrom, $dateTo];
    }

    public function index()
    {
        return view('reports.index');
    }

    // Sales Report
    public function sales(Request $request)
    {
        $this->normalizeDateRange($request);

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'group_by' => 'nullable|in:day,week,month,year',
        ]);

        $groupBy = $request->input('group_by', 'day');

        $sales = Sale::whereBetween('sale_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed')
            ->get(['sale_date', 'total_amount', 'tax_amount']);

        $totalSales = (float) $sales->sum('total_amount');
        $totalOrders = $sales->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $totalTax = (float) $sales->sum('tax_amount');

        $salesData = $sales
            ->groupBy(function ($sale) use ($groupBy) {
                return match ($groupBy) {
                    'week' => $sale->sale_date->format('o-\\WW'),
                    'month' => $sale->sale_date->format('Y-m'),
                    'year' => $sale->sale_date->format('Y'),
                    default => $sale->sale_date->format('Y-m-d'),
                };
            })
            ->map(function ($items, $period) {
                $count = $items->count();
                $total = (float) $items->sum('total_amount');
                $tax = (float) $items->sum('tax_amount');

                return [
                    'period' => $period,
                    'count' => $count,
                    'total' => $total,
                    'tax' => $tax,
                    'average' => $count > 0 ? $total / $count : 0,
                ];
            })
            ->sortBy('period')
            ->values();

        $summary = [
            'total_sales' => $totalSales,
            'total_transactions' => $totalOrders,
            'average_sale' => $averageOrder,
            'total_tax' => $totalTax,
        ];

        if ($request->input('format') === 'excel') {
            return $this->exportSalesExcel($salesData, $summary, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportSalesPdf($salesData, $summary, $validated);
        }

        return view('reports.sales', compact('salesData', 'summary', 'validated'));
    }

    // Product Sales Report
    public function productSales(Request $request)
    {
        $this->normalizeDateRange($request);

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $request->input('limit', 20);

        $categories = Category::orderBy('name')->get();

        $productSalesQuery = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.sale_date', [$validated['date_from'], $validated['date_to']])
            ->where('sales.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('COALESCE(categories.name, "Uncategorized") as category'),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
            ->orderByDesc('total_revenue');

        if ($request->filled('category_id')) {
            $productSalesQuery->where('products.category_id', $request->category_id);
        }

        $productSales = $productSalesQuery
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $quantitySold = (float) $row->total_quantity;
                $totalSales = (float) $row->total_revenue;

                return [
                    'name' => $row->name,
                    'sku' => $row->sku,
                    'category' => $row->category,
                    'quantity_sold' => $quantitySold,
                    'total_sales' => $totalSales,
                    'average_price' => $quantitySold > 0 ? $totalSales / $quantitySold : 0,
                ];
            });

        if ($request->input('format') === 'excel') {
            return $this->exportProductSalesExcel($productSales, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportProductSalesPdf($productSales, $validated);
        }

        return view('reports.product-sales', compact('productSales', 'validated', 'categories'));
    }

    // Inventory Report
    public function inventory(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::with(['category', 'unit', 'stock']);

        if ($request->boolean('low_stock_only')) {
            $query->lowStock();
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $inventory = $query->get()->map(function ($product) {
            $currentStock = (float) ($product->stock?->quantity ?? 0);
            $stockValue = ($product->stock?->quantity ?? 0) * $product->purchase_price;

            return [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => optional($product->category)->name ?? 'Uncategorized',
                'unit' => optional($product->unit)->name ?? 'N/A',
                'current_stock' => $currentStock,
                'purchase_price' => $product->purchase_price,
                'selling_price' => $product->selling_price,
                'stock_value' => $stockValue,
                'low_stock_alert' => $product->low_stock_alert,
                'is_low_stock' => $currentStock <= (float) $product->low_stock_alert,
            ];
        });

        $summary = [
            'total_products' => $inventory->count(),
            'total_stock_value' => (float) $inventory->sum('stock_value'),
            'low_stock_count' => $inventory->where('is_low_stock', true)->where('current_stock', '>', 0)->count(),
            'out_of_stock_count' => $inventory->where('current_stock', 0)->count(),
        ];

        $totalStockValue = $summary['total_stock_value'];

        if ($request->input('format') === 'excel') {
            return $this->exportInventoryExcel($inventory, $totalStockValue);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportInventoryPdf($inventory, $totalStockValue);
        }

        return view('reports.inventory', compact('inventory', 'categories', 'summary'));
    }

    // Stock Movement Report
    public function stockMovement(Request $request)
    {
        $this->normalizeDateRange($request);

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'product_id' => 'nullable|exists:products,id',
            'type' => 'nullable|in:in,out,adjustment',
        ]);

        $fromDateTime = Carbon::parse($validated['date_from'])->startOfDay();
        $toDateTime = Carbon::parse($validated['date_to'])->endOfDay();

        $query = StockLog::with(['product', 'creator'])
            ->whereBetween('created_at', [$fromDateTime, $toDateTime]);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->get()->map(function ($movement) {
            return [
                'date' => $movement->created_at->format('d M, Y h:i A'),
                'product' => optional($movement->product)->name ?? 'Unknown Product',
                'type' => $movement->type,
                'quantity' => (float) $movement->quantity,
                'reference' => $movement->reference_type
                    ? strtoupper($movement->reference_type) . ($movement->reference_id ? (' #' . $movement->reference_id) : '')
                    : 'N/A',
                'notes' => $movement->notes,
            ];
        });

        $stats = [
            'total_in' => StockLog::whereBetween('created_at', [$fromDateTime, $toDateTime])
                ->where('type', 'in')->sum('quantity'),
            'total_out' => StockLog::whereBetween('created_at', [$fromDateTime, $toDateTime])
                ->where('type', 'out')->sum('quantity'),
        ];

        return view('reports.stock-movement', compact('movements', 'stats', 'validated'));
    }

    // Expense Report
    public function expenses(Request $request)
    {
        $this->normalizeDateRange($request);

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:expense_categories,id',
        ]);

        $categories = ExpenseCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = Expense::with(['category', 'creator'])
            ->whereBetween('expense_date', [$validated['date_from'], $validated['date_to']]);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        $expenses = $query->latest('expense_date')->get();
        $totalExpenses = (float) $expenses->sum('amount');

        $summary = [
            'total_expenses' => $totalExpenses,
            'approved_expenses' => (float) $expenses->where('status', 'approved')->sum('amount'),
            'pending_expenses' => (float) $expenses->where('status', 'pending')->sum('amount'),
        ];

        $expensesByCategory = $expenses
            ->groupBy(function ($expense) {
                return optional($expense->category)->name ?? 'Uncategorized';
            })
            ->map(function ($items, $categoryName) use ($totalExpenses) {
                $categoryTotal = (float) $items->sum('amount');

                return [
                    'category' => $categoryName,
                    'count' => $items->count(),
                    'total' => $categoryTotal,
                    'percentage' => $totalExpenses > 0 ? ($categoryTotal / $totalExpenses) * 100 : 0,
                ];
            })
            ->values();

        if ($request->input('format') === 'excel') {
            return $this->exportExpensesExcel($expenses, $totalExpenses, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportExpensesPdf($expenses, $totalExpenses, $validated);
        }

        return view('reports.expenses', compact('categories', 'expensesByCategory', 'summary', 'validated'));
    }

    // Profit & Loss Report
    public function profitLoss(Request $request)
    {
        $this->normalizeDateRange($request);

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Sales Revenue
        $salesRevenue = Sale::whereBetween('sale_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalTransactions = Sale::whereBetween('sale_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed')
            ->count();

        // Cost of Goods Sold
        $cogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$validated['date_from'], $validated['date_to']])
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        // Returns
        $returns = ReturnModel::whereBetween('return_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'approved')
            ->sum('amount');

        $expensesByCategory = Expense::with('category')
            ->whereBetween('expense_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'approved')
            ->get()
            ->groupBy(function ($expense) {
                return optional($expense->category)->name ?? 'Uncategorized';
            })
            ->map(function ($items, $category) {
                return [
                    'category' => $category,
                    'amount' => (float) $items->sum('amount'),
                ];
            })
            ->values();

        $totalRevenue = (float) $salesRevenue - (float) $returns;
        $grossProfit = $totalRevenue - (float) $cogs;
        $netProfit = $grossProfit - (float) $expenses;
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $averageSaleValue = $totalTransactions > 0 ? (float) $salesRevenue / $totalTransactions : 0;

        $report = [
            'gross_sales' => (float) $salesRevenue,
            'returns' => (float) $returns,
            'total_revenue' => $totalRevenue,
            'cogs' => (float) $cogs,
            'gross_profit' => $grossProfit,
            'gross_profit_margin' => $grossProfitMargin,
            'total_expenses' => (float) $expenses,
            'expenses_by_category' => $expensesByCategory,
            'net_profit' => $netProfit,
            'net_profit_margin' => $netProfitMargin,
            'average_sale_value' => $averageSaleValue,
            'total_transactions' => $totalTransactions,
        ];

        $data = compact('report', 'validated');

        if ($request->input('format') === 'pdf') {
            return $this->exportProfitLossPdf($data);
        }

        return view('reports.profit-loss', $data);
    }

    // Customer Report
    public function customers(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $fromDate = $validated['from_date'] ?? now()->subDays(90)->toDateString();
        $toDate = $validated['to_date'] ?? now()->toDateString();

        $salesInRange = Sale::whereBetween('sale_date', [$fromDate, $toDate])
            ->where('status', 'completed');

        $totalRevenue = (float) (clone $salesInRange)->sum('total_amount');
        $totalTransactions = (int) (clone $salesInRange)->count();

        $summary = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'total_revenue' => $totalRevenue,
            'average_purchase' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0,
        ];

        $topCustomers = Customer::whereHas('sales', function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'completed')
                ->whereBetween('sale_date', [$fromDate, $toDate]);
        })
            ->with([
                'sales' => function ($query) use ($fromDate, $toDate) {
                    $query->where('status', 'completed')
                        ->whereBetween('sale_date', [$fromDate, $toDate])
                        ->latest('sale_date');
                }
            ])
            ->withCount([
                'sales as filtered_sales_count' => function ($query) use ($fromDate, $toDate) {
                    $query->where('status', 'completed')
                        ->whereBetween('sale_date', [$fromDate, $toDate]);
                }
            ])
            ->withSum([
                'sales as filtered_sales_sum_total_amount' => function ($query) use ($fromDate, $toDate) {
                    $query->where('status', 'completed')
                        ->whereBetween('sale_date', [$fromDate, $toDate]);
                }
            ], 'total_amount')
            ->orderByDesc('filtered_sales_sum_total_amount')
            ->limit(50)
            ->get()
            ->map(function ($customer) {
                $totalSpent = (float) ($customer->filtered_sales_sum_total_amount ?? 0);
                $purchaseCount = (int) ($customer->filtered_sales_count ?? 0);
                $lastPurchase = optional($customer->sales->first())->sale_date;

                return [
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'purchase_count' => $purchaseCount,
                    'total_spent' => $totalSpent,
                    'average_purchase' => $purchaseCount > 0 ? $totalSpent / $purchaseCount : 0,
                    'last_purchase' => $lastPurchase ? $lastPurchase->format('d M, Y') : 'N/A',
                ];
            });

        if ($request->input('format') === 'excel') {
            return $this->exportCustomersExcel($topCustomers);
        }

        return view('reports.customers', compact('summary', 'topCustomers'));
    }

    // Export helper methods (placeholders - actual Excel export logic would go here)
    private function exportSalesExcel($data, $stats, $params)
    {
        // Implementation would use Maatwebsite Excel export
        return response()->json(['message' => 'Excel export implementation needed']);
    }

    private function exportSalesPdf($data, $stats, $params)
    {
        $pdf = Pdf::loadView('reports.pdf.sales', compact('data', 'stats', 'params'));
        return $pdf->download('sales-report.pdf');
    }

    private function exportProductSalesExcel($data, $params)
    {
        return response()->json(['message' => 'Excel export implementation needed']);
    }

    private function exportProductSalesPdf($data, $params)
    {
        $pdf = Pdf::loadView('reports.pdf.product-sales', compact('data', 'params'));
        return $pdf->download('product-sales-report.pdf');
    }

    private function exportInventoryExcel($data, $totalValue)
    {
        return response()->json(['message' => 'Excel export implementation needed']);
    }

    private function exportInventoryPdf($data, $totalValue)
    {
        $pdf = Pdf::loadView('reports.pdf.inventory', compact('data', 'totalValue'));
        return $pdf->download('inventory-report.pdf');
    }

    private function exportExpensesExcel($data, $total, $params)
    {
        return response()->json(['message' => 'Excel export implementation needed']);
    }

    private function exportExpensesPdf($data, $total, $params)
    {
        $pdf = Pdf::loadView('reports.pdf.expenses', compact('data', 'total', 'params'));
        return $pdf->download('expenses-report.pdf');
    }

    private function exportProfitLossPdf($data)
    {
        $pdf = Pdf::loadView('reports.pdf.profit-loss', $data);
        return $pdf->download('profit-loss-report.pdf');
    }

    private function exportCustomersExcel($data)
    {
        return response()->json(['message' => 'Excel export implementation needed']);
    }
}
