<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Sales Report
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'nullable|in:day,month,year',
        ]);

        $query = Sale::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed');

        $totalSales = $query->sum('total_amount');
        $totalOrders = $query->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $groupBy = $request->input('group_by', 'day');

        if ($groupBy === 'day') {
            $salesData = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as total')
            )->groupBy('date')->orderBy('date')->get();
        } elseif ($groupBy === 'month') {
            $salesData = $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as total')
            )->groupBy('date')->orderBy('date')->get();
        } else {
            $salesData = $query->select(
                DB::raw('YEAR(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as total')
            )->groupBy('date')->orderBy('date')->get();
        }

        $stats = compact('totalSales', 'totalOrders', 'averageOrder');

        if ($request->input('format') === 'excel') {
            return $this->exportSalesExcel($salesData, $stats, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportSalesPdf($salesData, $stats, $validated);
        }

        return view('reports.sales', compact('salesData', 'stats', 'validated'));
    }

    // Product Sales Report
    public function productSales(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $request->input('limit', 20);

        $productSales = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$validated['date_from'], $validated['date_to']])
            ->where('sales.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        if ($request->input('format') === 'excel') {
            return $this->exportProductSalesExcel($productSales, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportProductSalesPdf($productSales, $validated);
        }

        return view('reports.product-sales', compact('productSales', 'validated'));
    }

    // Inventory Report
    public function inventory(Request $request)
    {
        $query = Product::with(['category', 'unit', 'stock']);

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get()->map(function ($product) {
            $stockValue = ($product->stock?->quantity ?? 0) * $product->purchase_price;
            return [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category->name,
                'unit' => $product->unit->name,
                'quantity' => $product->stock?->quantity ?? 0,
                'purchase_price' => $product->purchase_price,
                'selling_price' => $product->selling_price,
                'stock_value' => $stockValue,
                'low_stock_alert' => $product->low_stock_alert,
                'is_low_stock' => ($product->stock?->quantity ?? 0) <= $product->low_stock_alert,
            ];
        });

        $totalStockValue = $products->sum('stock_value');
        $lowStockCount = $products->where('is_low_stock', true)->count();

        if ($request->input('format') === 'excel') {
            return $this->exportInventoryExcel($products, $totalStockValue);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportInventoryPdf($products, $totalStockValue);
        }

        return view('reports.inventory', compact('products', 'totalStockValue', 'lowStockCount'));
    }

    // Stock Movement Report
    public function stockMovement(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'product_id' => 'nullable|exists:products,id',
            'type' => 'nullable|in:in,out,adjustment',
        ]);

        $query = StockLog::with(['product', 'createdBy'])
            ->whereBetween('created_at', [$validated['date_from'], $validated['date_to']]);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->latest()->paginate(50);

        $stats = [
            'total_in' => StockLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                ->where('type', 'in')->sum('quantity'),
            'total_out' => StockLog::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
                ->where('type', 'out')->sum('quantity'),
        ];

        return view('reports.stock-movement', compact('movements', 'stats', 'validated'));
    }

    // Expense Report
    public function expenses(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:expense_categories,id',
        ]);

        $query = Expense::with(['category', 'createdBy'])
            ->whereBetween('expense_date', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'approved');

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        $expenses = $query->latest('expense_date')->get();
        $totalExpenses = $expenses->sum('amount');

        $expensesByCategory = $expenses->groupBy('category.name')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        });

        if ($request->input('format') === 'excel') {
            return $this->exportExpensesExcel($expenses, $totalExpenses, $validated);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportExpensesPdf($expenses, $totalExpenses, $validated);
        }

        return view('reports.expenses', compact('expenses', 'totalExpenses', 'expensesByCategory', 'validated'));
    }

    // Profit & Loss Report
    public function profitLoss(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        // Sales Revenue
        $salesRevenue = Sale::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Cost of Goods Sold
        $cogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$validated['date_from'], $validated['date_to']])
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

        $grossProfit = $salesRevenue - $cogs - $returns;
        $netProfit = $grossProfit - $expenses;
        $profitMargin = $salesRevenue > 0 ? ($netProfit / $salesRevenue) * 100 : 0;

        $data = compact('salesRevenue', 'cogs', 'returns', 'expenses', 'grossProfit', 'netProfit', 'profitMargin', 'validated');

        if ($request->input('format') === 'pdf') {
            return $this->exportProfitLossPdf($data);
        }

        return view('reports.profit-loss', $data);
    }

    // Customer Report
    public function customers(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'total_amount')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_sum_total_amount')
            ->limit(50)
            ->get();

        if ($request->input('format') === 'excel') {
            return $this->exportCustomersExcel($customers);
        }

        return view('reports.customers', compact('customers'));
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
