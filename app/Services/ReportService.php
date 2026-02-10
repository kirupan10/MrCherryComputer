<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get sales summary for a date range
     */
    public function getSalesSummary(string $dateFrom, string $dateTo): array
    {
        $sales = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed');

        return [
            'total_sales' => $sales->sum('total_amount'),
            'total_orders' => $sales->count(),
            'average_order_value' => $sales->avg('total_amount') ?? 0,
            'total_tax' => $sales->sum('tax_amount'),
            'total_discount' => $sales->sum('discount_amount'),
        ];
    }

    /**
     * Get daily sales breakdown
     */
    public function getDailySales(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        return Sale::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(discount_amount) as discount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(string $dateFrom, string $dateTo, int $limit = 10): \Illuminate\Support\Collection
    {
        return SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
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
    }

    /**
     * Get sales by payment method
     */
    public function getSalesByPaymentMethod(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        return DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->where('sales.status', 'completed')
            ->where('payments.status', 'completed')
            ->select(
                'payments.payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(payments.amount) as total')
            )
            ->groupBy('payments.payment_method')
            ->get();
    }

    /**
     * Get inventory summary
     */
    public function getInventorySummary(): array
    {
        $products = Product::with(['stock', 'category'])
            ->where('is_active', true)
            ->get();

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return ($product->stock?->quantity ?? 0) * $product->purchase_price;
        });
        $lowStockCount = $products->filter(function ($product) {
            return ($product->stock?->quantity ?? 0) <= $product->low_stock_alert;
        })->count();
        $outOfStockCount = $products->filter(function ($product) {
            return ($product->stock?->quantity ?? 0) == 0;
        })->count();

        return compact('totalProducts', 'totalStockValue', 'lowStockCount', 'outOfStockCount');
    }

    /**
     * Get stock movement summary
     */
    public function getStockMovementSummary(string $dateFrom, string $dateTo): array
    {
        $movements = StockLog::whereBetween('created_at', [$dateFrom, $dateTo]);

        return [
            'total_in' => $movements->where('type', 'in')->sum('quantity'),
            'total_out' => $movements->where('type', 'out')->sum('quantity'),
            'total_adjustments' => $movements->where('type', 'adjustment')->count(),
            'movements_count' => $movements->count(),
        ];
    }

    /**
     * Get expense summary
     */
    public function getExpenseSummary(string $dateFrom, string $dateTo): array
    {
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'approved');

        $byCategory = Expense::with('category')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'approved')
            ->get()
            ->groupBy('category.name')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ];
            });

        return [
            'total_expenses' => $expenses->sum('amount'),
            'total_count' => $expenses->count(),
            'by_category' => $byCategory,
        ];
    }

    /**
     * Get profit and loss report
     */
    public function getProfitLoss(string $dateFrom, string $dateTo): array
    {
        // Revenue
        $salesRevenue = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Cost of Goods Sold
        $cogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        // Returns
        $returns = ReturnModel::whereBetween('return_date', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Operating Expenses
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->where('status', 'approved')
            ->sum('amount');

        // Calculations
        $netSalesRevenue = $salesRevenue - $returns;
        $grossProfit = $netSalesRevenue - $cogs;
        $netProfit = $grossProfit - $expenses;
        $grossProfitMargin = $netSalesRevenue > 0 ? ($grossProfit / $netSalesRevenue) * 100 : 0;
        $netProfitMargin = $netSalesRevenue > 0 ? ($netProfit / $netSalesRevenue) * 100 : 0;

        return [
            'sales_revenue' => round($salesRevenue, 2),
            'returns' => round($returns, 2),
            'net_sales_revenue' => round($netSalesRevenue, 2),
            'cogs' => round($cogs, 2),
            'gross_profit' => round($grossProfit, 2),
            'expenses' => round($expenses, 2),
            'net_profit' => round($netProfit, 2),
            'gross_profit_margin' => round($grossProfitMargin, 2),
            'net_profit_margin' => round($netProfitMargin, 2),
        ];
    }

    /**
     * Get customer analytics
     */
    public function getCustomerAnalytics(): \Illuminate\Support\Collection
    {
        return Customer::withCount('sales')
            ->withSum('sales', 'total_amount')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_sum_total_amount')
            ->limit(50)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'total_orders' => $customer->sales_count,
                    'total_spent' => $customer->sales_sum_total_amount ?? 0,
                    'average_order_value' => $customer->sales_count > 0 
                        ? ($customer->sales_sum_total_amount ?? 0) / $customer->sales_count 
                        : 0,
                ];
            });
    }

    /**
     * Get sales trends (comparison with previous period)
     */
    public function getSalesTrends(string $dateFrom, string $dateTo): array
    {
        $currentPeriod = Carbon::parse($dateFrom);
        $endPeriod = Carbon::parse($dateTo);
        $days = $currentPeriod->diffInDays($endPeriod);

        // Current period
        $currentSales = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        $currentOrders = Sale::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->count();

        // Previous period
        $previousDateFrom = $currentPeriod->copy()->subDays($days)->format('Y-m-d');
        $previousDateTo = $currentPeriod->copy()->subDay()->format('Y-m-d');

        $previousSales = Sale::whereBetween('created_at', [$previousDateFrom, $previousDateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        $previousOrders = Sale::whereBetween('created_at', [$previousDateFrom, $previousDateTo])
            ->where('status', 'completed')
            ->count();

        // Calculate trends
        $salesGrowth = $previousSales > 0 
            ? (($currentSales - $previousSales) / $previousSales) * 100 
            : 0;

        $ordersGrowth = $previousOrders > 0 
            ? (($currentOrders - $previousOrders) / $previousOrders) * 100 
            : 0;

        return [
            'current_sales' => round($currentSales, 2),
            'current_orders' => $currentOrders,
            'previous_sales' => round($previousSales, 2),
            'previous_orders' => $previousOrders,
            'sales_growth' => round($salesGrowth, 2),
            'orders_growth' => round($ordersGrowth, 2),
        ];
    }

    /**
     * Get hourly sales distribution
     */
    public function getHourlySales(string $date): \Illuminate\Support\Collection
    {
        return Sale::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get category-wise sales
     */
    public function getCategorySales(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->where('sales.status', 'completed')
            ->select(
                'categories.name as category',
                DB::raw('COUNT(DISTINCT sale_items.id) as items_sold'),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }
}
