<?php

namespace App\Http\Controllers;

use App\Models\BusinessTransaction;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ReturnSale;
use App\Models\CreditSale;
use App\Models\CreditPayment;
use App\Models\CreditPurchase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    /**
     * Finance Dashboard - Overview of all financial metrics
     */
    public function dashboard(Request $request)
    {
        // Authorization check - staff cannot access finance dashboard
        if (!auth()->user()->canAccessFinanceDashboard()) {
            abort(403, 'You do not have permission to access the Finance Dashboard.');
        }

        $shopId = auth()->user()->shop_id;
        $month = $request->get('month', now()->format('Y-m'));
        $year = $request->get('year', now()->year);

        try {
            $selectedDate = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $selectedDate = now();
            $month = $selectedDate->format('Y-m');
        }

        // Revenue calculations (turnover)
        $revenue = $this->calculateRevenue($shopId, $selectedDate);

        // Gross profit from sales
        $grossProfit = $this->calculateGrossProfit($shopId, $selectedDate);

        // Expense calculations
        $expenses = $this->calculateExpenses($shopId, $selectedDate);

        // Net profit/loss = Gross profit - Total expenses
        $netProfit = $grossProfit - $expenses['total'];

        // Previous month comparison
        $prevMonth = $selectedDate->copy()->subMonth();
        $prevRevenue = $this->calculateRevenue($shopId, $prevMonth);
        $prevGrossProfit = $this->calculateGrossProfit($shopId, $prevMonth);
        $prevExpenses = $this->calculateExpenses($shopId, $prevMonth);
        $prevNetProfit = $prevGrossProfit - $prevExpenses['total'];

        // Calculate percentage changes
        $revenueChange = $prevRevenue['total'] > 0
            ? (($revenue['total'] - $prevRevenue['total']) / $prevRevenue['total']) * 100
            : 0;
        $expenseChange = $prevExpenses['total'] > 0
            ? (($expenses['total'] - $prevExpenses['total']) / $prevExpenses['total']) * 100
            : 0;
        $profitChange = $prevNetProfit != 0
            ? (($netProfit - $prevNetProfit) / abs($prevNetProfit)) * 100
            : 0;

        // Get monthly trend (last 3 months)
        $monthlyTrend = $this->getMonthlyTrend($shopId, 3);

        // Outstanding credit sales
        $outstandingCredit = CreditSale::where('shop_id', $shopId)
            ->where('status', 'pending')
            ->sum('due_amount');

        // Credit Purchase Statistics
        $creditPurchaseStats = [
            'total_purchases' => CreditPurchase::where('shop_id', $shopId)
                ->whereMonth('purchase_date', $selectedDate->month)
                ->whereYear('purchase_date', $selectedDate->year)
                ->sum('total_amount') ?? 0,
            'outstanding_due' => CreditPurchase::where('shop_id', $shopId)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('due_amount') ?? 0,
            'pending_count' => CreditPurchase::where('shop_id', $shopId)
                ->where('status', 'pending')
                ->count() ?? 0,
            'overdue_count' => CreditPurchase::where('shop_id', $shopId)
                ->whereIn('status', ['pending', 'partial'])
                ->where('due_date', '<', now()->toDateString())
                ->count() ?? 0,
        ];

        return view('finance.dashboard', compact(
            'revenue',
            'grossProfit',
            'expenses',
            'netProfit',
            'revenueChange',
            'expenseChange',
            'profitChange',
            'monthlyTrend',
            'outstandingCredit',
            'creditPurchaseStats',
            'month',
            'year',
            'selectedDate'
        ));
    }

    /**
     * Profit & Loss Statement
     */
    public function profitLoss(Request $request)
    {
        // Authorization check - staff cannot access P&L statement
        if (!auth()->user()->canAccessFinanceDashboard()) {
            abort(403, 'You do not have permission to access the Profit & Loss Statement.');
        }

        $shopId = auth()->user()->shop_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'month'); // month or year

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Calculate revenue breakdown
        $revenueBreakdown = $this->getRevenueBreakdown($shopId, $start, $end);

        // Calculate expense breakdown
        $expenseBreakdown = $this->getExpenseBreakdown($shopId, $start, $end);

        // Calculate totals
        $totalRevenue = collect($revenueBreakdown)->sum('amount');
        $totalExpenses = collect($expenseBreakdown)->sum('amount');

        // Calculate gross profit from sales
        $grossProfit = $this->calculateGrossProfitForPeriod($shopId, $start, $end);

        // Net profit = Gross profit - Total expenses
        $netProfit = $grossProfit - $totalExpenses;
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Get period-by-period comparison
        $periodComparison = $this->getPeriodComparison($shopId, $start, $end, $groupBy);

        return view('finance.profit-loss', compact(
            'revenueBreakdown',
            'expenseBreakdown',
            'totalRevenue',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'netProfitMargin',
            'periodComparison',
            'startDate',
            'endDate',
            'groupBy'
        ));
    }

    /**
     * Monthly Financial Report
     */
    public function monthlyReport(Request $request)
    {
        // Authorization check - staff cannot access monthly reports
        if (!auth()->user()->canAccessFinanceDashboard()) {
            abort(403, 'You do not have permission to access the Monthly Financial Report.');
        }

        $shopId = auth()->user()->shop_id;
        $month = $request->get('month', now()->format('Y-m'));

        try {
            $date = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            $date = now();
            $month = $date->format('Y-m');
        }

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Get all financial data for the month
        $revenue = $this->calculateRevenue($shopId, $date);
        $grossProfit = $this->calculateGrossProfit($shopId, $date);
        $expenses = $this->calculateExpenses($shopId, $date);
        $netProfit = $grossProfit - $expenses['total'];

        // Get detailed transactions
        $salesOrders = Order::where('shop_id', $shopId)
            ->with('customer')
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate profit for each order with sorting
        $sortBy = $request->get('sort_sales', 'date'); // 'date' or 'profit'
        $ordersWithProfit = $this->getOrdersWithProfit($salesOrders, $sortBy);

        $expenseRecords = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $businessTransactions = BusinessTransaction::where('shop_id', $shopId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Daily breakdown - show last 7 days by default, or full month if requested
        $showFullMonth = $request->get('show_full_month', false);
        if ($showFullMonth) {
            $dailyBreakdown = $this->getDailyBreakdown($shopId, $startDate, $endDate);
        } else {
            // Last 7 days from today (or from end of month if viewing past month)
            $today = now();
            $monthEnd = $endDate->copy();
            $actualEndDate = $today->lte($monthEnd) ? $today : $monthEnd;
            $last7DaysStart = $actualEndDate->copy()->subDays(6); // 7 days including today
            $dailyBreakdown = $this->getDailyBreakdown($shopId, $last7DaysStart, $actualEndDate);
        }

        return view('finance.monthly-report', compact(
            'revenue',
            'grossProfit',
            'netProfit',
            'expenses',
            'salesOrders',
            'ordersWithProfit',
            'expenseRecords',
            'businessTransactions',
            'dailyBreakdown',
            'month',
            'startDate',
            'endDate',
            'showFullMonth',
            'sortBy'
        ));
    }

    /**
     * Calculate revenue for a given period
     */
    private function calculateRevenue($shopId, $date)
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Total sales - ALL orders (all treated as completed)
        $totalSales = Order::where('shop_id', $shopId)
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString())
            ->sum('total');

        // Breakdown by payment type
        $cashSales = Order::where('shop_id', $shopId)
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString())
            ->where('payment_type', '!=', 'Credit')
            ->sum('total');

        // Credit sales still outstanding
        $creditSales = CreditSale::where('shop_id', $shopId)
            ->whereDate('sale_date', '>=', $startDate->toDateString())
            ->whereDate('sale_date', '<=', $endDate->toDateString())
            ->where('status', 'pending')
            ->sum('due_amount');

        // Returns (negative revenue)
        $returns = ReturnSale::where('shop_id', $shopId)
            ->whereDate('return_date', '>=', $startDate->toDateString())
            ->whereDate('return_date', '<=', $endDate->toDateString())
            ->sum('total');

        // Total turnover = All completed sales - returns
        $totalTurnover = $totalSales - $returns;

        return [
            'sales' => $cashSales,
            'credit_sales' => $creditSales,
            'returns' => $returns,
            'total' => $totalTurnover
        ];
    }

    /**
     * Calculate gross profit from sales (selling price - buying price)
     * Note: Gross profit can include losses where items were sold below buying price.
     * The formula (total_sales - total_cost) automatically handles this:
     * - When selling_price > buying_price: contributes positive profit
     * - When selling_price < buying_price: contributes negative profit (loss)
     * Total gross profit is the sum of all profits minus all losses
     */
    private function calculateGrossProfit($shopId, $date)
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $isCurrentMonth = $startDate->isSameMonth(now()) && $startDate->isSameYear(now());

        // Get all completed orders in the period
        $ordersQuery = Order::where('shop_id', $shopId)
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString());

        $orderIds = $ordersQuery->pluck('id');
        $totalSales = (float) $ordersQuery->sum('total');

        // Calculate total cost from order details, then derive gross profit from billed sales
        $totalCost = OrderDetails::whereIn('order_id', $orderIds)
            ->with('product')
            ->get()
            ->sum(function ($detail) use ($isCurrentMonth) {
                if ($isCurrentMonth) {
                    $buyingPrice = $detail->product && $detail->product->buying_price
                        ? $detail->product->buying_price
                        : null;
                } else {
                    $buyingPrice = $detail->buying_price;

                    if (is_null($buyingPrice) && $detail->product && $detail->product->buying_price) {
                        $buyingPrice = $detail->product->buying_price;
                    }
                }

                if (is_null($buyingPrice) || $buyingPrice <= 0) {
                    return 0;
                }

                $buyingPrice = max(0, (float)$buyingPrice);
                return $buyingPrice * $detail->quantity;
            });

        $grossProfit = $totalSales - $totalCost;

        // Subtract return costs
        $returnIds = ReturnSale::where('shop_id', $shopId)
            ->whereBetween('return_date', [$startDate, $endDate])
            ->pluck('id');

        if ($returnIds->isNotEmpty()) {
            $returnCostImpact = DB::table('return_sale_items')
                ->whereIn('return_sale_id', $returnIds)
                ->join('products', 'return_sale_items.product_id', '=', 'products.id')
                ->whereNotNull('products.buying_price')
                ->where('products.buying_price', '>', 0)
                ->select(
                    DB::raw('SUM((return_sale_items.unitcost - products.buying_price) * return_sale_items.quantity) as profit_loss')
                )
                ->value('profit_loss') ?? 0;

            $grossProfit -= $returnCostImpact;
        }

        return $grossProfit;
    }

    /**
     * Calculate gross profit for a custom period (start and end dates)
     */
    private function calculateGrossProfitForPeriod($shopId, $startDate, $endDate)
    {
        $isCurrentMonthPeriod = $startDate->copy()->startOfMonth()->isSameDay(now()->copy()->startOfMonth())
            && $endDate->copy()->endOfMonth()->isSameDay(now()->copy()->endOfMonth());

        // Get all completed orders in the period
        $ordersQuery = Order::where('shop_id', $shopId)
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString());

        $orderIds = $ordersQuery->pluck('id');
        $totalSales = (float) $ordersQuery->sum('total');

        // Calculate total cost from order details, then derive gross profit from billed sales
        $totalCost = OrderDetails::whereIn('order_id', $orderIds)
            ->with('product')
            ->get()
            ->sum(function ($detail) use ($isCurrentMonthPeriod) {
                if ($isCurrentMonthPeriod) {
                    $buyingPrice = $detail->product && $detail->product->buying_price
                        ? $detail->product->buying_price
                        : null;
                } else {
                    $buyingPrice = $detail->buying_price;

                    if (is_null($buyingPrice) && $detail->product && $detail->product->buying_price) {
                        $buyingPrice = $detail->product->buying_price;
                    }
                }

                if (is_null($buyingPrice) || $buyingPrice <= 0) {
                    return 0;
                }

                $buyingPrice = max(0, (float)$buyingPrice);
                return $buyingPrice * $detail->quantity;
            });

        $grossProfit = $totalSales - $totalCost;

        // Subtract return costs
        $returnIds = ReturnSale::where('shop_id', $shopId)
            ->whereBetween('return_date', [$startDate, $endDate])
            ->pluck('id');

        if ($returnIds->isNotEmpty()) {
            $returnCostImpact = DB::table('return_sale_items')
                ->whereIn('return_sale_id', $returnIds)
                ->join('products', 'return_sale_items.product_id', '=', 'products.id')
                ->whereNotNull('products.buying_price')
                ->where('products.buying_price', '>', 0)
                ->select(
                    DB::raw('SUM((return_sale_items.unitcost - products.buying_price) * return_sale_items.quantity) as profit_loss')
                )
                ->value('profit_loss') ?? 0;

            $grossProfit -= $returnCostImpact;
        }

        return $grossProfit;
    }

    /**
     * Calculate expenses for a given period
     */
    private function calculateExpenses($shopId, $date)
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Operating expenses
        $operatingExpenses = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Business transactions (purchases, etc.)
        $businessExpenses = BusinessTransaction::where('shop_id', $shopId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_type', 'purchase')
            ->where('status', 'completed')
            ->sum('net_amount');

        return [
            'operating' => $operatingExpenses,
            'business' => $businessExpenses,
            'total' => $operatingExpenses + $businessExpenses
        ];
    }

    /**
     * Get monthly trend data
     */
    private function getMonthlyTrend($shopId, $months = 12)
    {
        $trend = [];
        $currentDate = now();

        for ($i = 0; $i < $months; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $revenue = $this->calculateRevenue($shopId, $date);
            $grossProfit = $this->calculateGrossProfit($shopId, $date);
            $expenses = $this->calculateExpenses($shopId, $date);

            $trend[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue['total'],
                'expenses' => $expenses['total'],
                'profit' => $grossProfit - $expenses['total']
            ];
        }

        return $trend;
    }

    /**
     * Get revenue breakdown by category
     */
    private function getRevenueBreakdown($shopId, $start, $end)
    {
        $breakdown = [];

        // Sales
        $sales = Order::where('shop_id', $shopId)
            ->whereDate('created_at', '>=', $start->toDateString())
            ->whereDate('created_at', '<=', $end->toDateString())
            ->sum('total');
        if ($sales > 0) {
            $breakdown[] = ['category' => 'Product Sales', 'amount' => $sales];
        }

        // Credit payments
        $creditPayments = CreditPayment::where('shop_id', $shopId)
            ->whereBetween('payment_date', [$start, $end])
            ->sum('payment_amount');
        if ($creditPayments > 0) {
            $breakdown[] = ['category' => 'Credit Payments Received', 'amount' => $creditPayments];
        }

        return $breakdown;
    }

    /**
     * Get expense breakdown by type
     */
    private function getExpenseBreakdown($shopId, $start, $end)
    {
        // Group expenses by type
        $expenses = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$start, $end])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->type ?: 'Other',
                    'amount' => $item->total
                ];
            })
            ->toArray();

        // Add business transaction expenses (purchases only)
        $businessExpenses = BusinessTransaction::where('shop_id', $shopId)
            ->whereBetween('transaction_date', [$start, $end])
            ->where('transaction_type', 'purchase')
            ->where('status', 'completed')
            ->select('transaction_type', DB::raw('SUM(net_amount) as total'))
            ->groupBy('transaction_type')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => ucfirst($item->transaction_type),
                    'amount' => $item->total
                ];
            })
            ->toArray();

        return array_merge($expenses, $businessExpenses);
    }

    /**
     * Get period-by-period comparison
     */
    private function getPeriodComparison($shopId, $start, $end, $groupBy)
    {
        $comparison = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($groupBy === 'month') {
                $periodStart = $current->copy()->startOfMonth();
                $periodEnd = $current->copy()->endOfMonth();
                $label = $current->format('M Y');
                $current->addMonth();
            } else {
                $periodStart = $current->copy()->startOfYear();
                $periodEnd = $current->copy()->endOfYear();
                $label = $current->format('Y');
                $current->addYear();
            }

            $revenue = $this->calculateRevenue($shopId, $periodStart);
            $grossProfit = $this->calculateGrossProfitForPeriod($shopId, $periodStart, $periodEnd);
            $expenses = $this->calculateExpenses($shopId, $periodStart);

            $comparison[] = [
                'period' => $label,
                'revenue' => $revenue['total'],
                'expenses' => $expenses['total'],
                'profit' => $grossProfit - $expenses['total']
            ];
        }

        return $comparison;
    }

    /**
     * Get daily breakdown for a period
     */
    private function getDailyBreakdown($shopId, $start, $end)
    {
        $breakdown = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            $revenue = Order::where('shop_id', $shopId)
                ->whereDate('created_at', '>=', $dayStart->toDateString())
                ->whereDate('created_at', '<=', $dayEnd->toDateString())
                ->sum('total');

            $expenses = Expense::where('shop_id', $shopId)
                ->whereBetween('expense_date', [$dayStart, $dayEnd])
                ->sum('amount');

            $breakdown[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('D, d M'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses
            ];

            $current->addDay();
        }

        return $breakdown;
    }

    /**
     * Calculate profit for each order by summing order details
     */
    private function getOrdersWithProfit($orders, $sortBy = 'date')
    {
        $result = $orders->map(function ($order) {
            $useCurrentMonthPrice = $order->created_at->isCurrentMonth();

            $totalProfit = 0;
            $totalCost = 0;

            foreach ($order->details as $detail) {
                if ($useCurrentMonthPrice) {
                    $buyingPrice = $detail->product && $detail->product->buying_price
                        ? $detail->product->buying_price
                        : null;
                } else {
                    $buyingPrice = $detail->buying_price;

                    if (is_null($buyingPrice) && $detail->product && $detail->product->buying_price) {
                        $buyingPrice = $detail->product->buying_price;
                    }
                }

                if (is_null($buyingPrice) || $buyingPrice <= 0) {
                    $buyingPrice = 0;
                }

                $buyingPrice = max(0, (float)$buyingPrice);

                // Calculate profit/loss: (selling_price - buying_price) * quantity
                // When selling_price < buying_price, this will be NEGATIVE (a loss)
                // Losses are subtracted from total profit automatically
                $itemProfit = ($detail->unitcost - $buyingPrice) * $detail->quantity;
                $totalProfit += $itemProfit;  // This adds profit or subtracts loss
                $totalCost += $buyingPrice * $detail->quantity;
            }

            return [
                'order' => $order,
                'profit' => $totalProfit,
                'margin' => $order->total > 0 ? ($totalProfit / $order->total) * 100 : 0
            ];
        });

        // Sort by requested field
        if ($sortBy === 'profit') {
            return $result->sortByDesc('profit');
        } else {
            // Default sort by date (latest first)
            return $result->sortByDesc(function ($item) {
                return $item['order']->created_at;
            });
        }
    }

    /**
     * Verify and update profit for a specific order by invoice number
     */
    public function verifyProfit(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|string'
        ]);

        $shopId = auth()->user()->shop_id;
        $invoiceNo = $request->input('invoice_no');

        // Find the order
        $order = Order::where('shop_id', $shopId)
            ->where('invoice_no', $invoiceNo)
            ->with(['details.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found: ' . $invoiceNo
            ], 404);
        }

        $details = [];
        $totalProfit = 0;
        $totalCost = 0;
        $updatedCount = 0;

        foreach ($order->details as $detail) {
            $product = $detail->product;
            $currentBuyingPrice = $product ? $product->buying_price : 0;
            $storedBuyingPrice = $detail->buying_price;

            // Calculate profit with current and stored buying prices
            // Note: When selling_price < buying_price, profit will be NEGATIVE (a loss)
            $profitWithStored = $storedBuyingPrice ? ($detail->unitcost - $storedBuyingPrice) * $detail->quantity : null;
            $profitWithCurrent = ($detail->unitcost - $currentBuyingPrice) * $detail->quantity;

            $cost = $currentBuyingPrice * $detail->quantity;
            $totalCost += $cost;
            $totalProfit += $profitWithCurrent;  // Adds profit or subtracts loss

            // Update buying_price if it's different from current product price
            $wasUpdated = false;
            if ($storedBuyingPrice != $currentBuyingPrice) {
                $detail->buying_price = $currentBuyingPrice;
                $detail->save();
                $wasUpdated = true;
                $updatedCount++;
            }

            // Get product name from product or fallback to saved name
            $productName = 'Unknown Product';
            if ($product && $product->name) {
                $productName = $product->name;
            } elseif ($detail->product_name) {
                $productName = $detail->product_name;
            }

            $details[] = [
                'product_name' => $productName,
                'quantity' => $detail->quantity,
                'unit_cost' => $detail->unitcost,
                'stored_buying_price' => $storedBuyingPrice,
                'current_buying_price' => $currentBuyingPrice,
                'profit_with_stored' => $profitWithStored,
                'profit_with_current' => $profitWithCurrent,
                'was_updated' => $wasUpdated,
            ];
        }

        $margin = $order->total > 0 ? ($totalProfit / $order->total) * 100 : 0;

        return response()->json([
            'success' => true,
            'invoice_no' => $invoiceNo,
            'order_date' => $order->created_at->format('M d, Y'),
            'customer' => $order->customer ? $order->customer->name : 'Walk-in',
            'sale_amount' => $order->total,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => $margin,
            'items_updated' => $updatedCount,
            'details' => $details,
        ]);
    }

    /**
     * Update KPI calculations after profit verification
     * This recalculates and caches KPI metrics
     */
    public function updateKpiCalculations(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|string'
        ]);

        $shopId = auth()->user()->shop_id;
        $invoiceNo = $request->input('invoice_no');

        // Find the order
        $order = Order::where('shop_id', $shopId)
            ->where('invoice_no', $invoiceNo)
            ->with(['details.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found: ' . $invoiceNo
            ], 404);
        }

        try {
            // Clear KPI cache to force recalculation
            Cache::forget('kpi_inventory_value');
            Cache::forget('kpi_monthly_revenue');
            Cache::forget('kpi_gross_profit');
            Cache::forget('kpi_net_profit');

            // Recalculate affected KPIs
            $kpiService = new \App\Services\KpiService();

            // Force recalculation of in-stock value
            $inventoryValue = $kpiService->inStockValue($shopId);

            // Return success with updated metrics
            return response()->json([
                'success' => true,
                'message' => 'KPI calculations updated successfully',
                'invoice_no' => $invoiceNo,
                'inventory_value' => $inventoryValue,
                'cached_keys' => [
                    'kpi_inventory_value',
                    'kpi_monthly_revenue',
                    'kpi_gross_profit',
                    'kpi_net_profit'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating KPI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the stored profit in the database by updating order_details with current buying prices
     */
    public function updateStoredProfit(Request $request)
    {
        try {
            $invoiceNo = $request->input('invoice_no');
            Log::info('Update stored profit called for invoice: ' . $invoiceNo);

            $order = Order::where('invoice_no', $invoiceNo)->first();
            if (!$order) {
                Log::warning('Order not found: ' . $invoiceNo);
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Clear model cache
            $order->refresh();

            // Update all order details with current buying prices from products table
            $totalCost = 0;
            $orderDetails = $order->details()->with('product')->get();
            $updatedCount = 0;

            foreach ($orderDetails as $detail) {
                $product = $detail->product;
                if ($product) {
                    // Refresh product to get latest price
                    $product->refresh();

                    $oldBuyingPrice = $detail->buying_price;
                    $currentBuyingPrice = $product->buying_price ?? $detail->buying_price;

                    // Update the order detail with current buying price
                    DB::table('order_details')
                        ->where('id', $detail->id)
                        ->update(['buying_price' => $currentBuyingPrice]);

                    if ($oldBuyingPrice != $currentBuyingPrice) {
                        Log::info("Updated {$product->name}: {$oldBuyingPrice} -> {$currentBuyingPrice}");
                        $updatedCount++;
                    }

                    $totalCost += $currentBuyingPrice * $detail->quantity;
                }
            }

            // Calculate new profit and margin
            $totalProfit = $order->total - $totalCost;
            $profitMargin = $order->total > 0 ? ($totalProfit / $order->total) * 100 : 0;

            // Clear all KPI caches to force recalculation
            Cache::forget('kpi_inventory_value');
            Cache::forget('kpi_monthly_revenue');
            Cache::forget('kpi_gross_profit');
            Cache::forget('kpi_net_profit');

            Log::info("Updated {$updatedCount} items. New profit: {$totalProfit}");

            return response()->json([
                'success' => true,
                'invoice_no' => $invoiceNo,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => round($profitMargin, 1),
                'items_updated' => $updatedCount,
                'message' => 'Stored profit updated successfully. Page will reload.',
                'reload' => true
            ]);
        } catch (Exception $e) {
            Log::error('Error updating stored profit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating stored profit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update all sales profit for a specific month
     */
    public function bulkUpdateProfit(Request $request)
    {
        try {
            $month = $request->input('month');

            if (!$month) {
                return response()->json([
                    'success' => false,
                    'message' => 'Month is required'
                ], 400);
            }

            Log::info('Bulk update profit called for month: ' . $month);

            // Parse the month
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $month);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid month format'
                ], 400);
            }

            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $shopId = auth()->user()->shop_id;

            // Get all orders for the month
            $orders = Order::where('shop_id', $shopId)
                ->whereDate('created_at', '>=', $startDate->toDateString())
                ->whereDate('created_at', '<=', $endDate->toDateString())
                ->with('details.product')
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found for the selected month'
                ], 404);
            }

            $totalOrders = $orders->count();
            $totalItemsUpdated = 0;
            $processedOrders = 0;

            // Update each order's details
            foreach ($orders as $order) {
                foreach ($order->details as $detail) {
                    $product = $detail->product;
                    if ($product) {
                        // Refresh product to get latest price
                        $product->refresh();

                        $oldBuyingPrice = $detail->buying_price;
                        $currentBuyingPrice = $product->buying_price ?? $detail->buying_price;

                        // Update the order detail with current buying price
                        if ($oldBuyingPrice != $currentBuyingPrice) {
                            DB::table('order_details')
                                ->where('id', $detail->id)
                                ->update(['buying_price' => $currentBuyingPrice]);

                            $totalItemsUpdated++;
                        }
                    }
                }
                $processedOrders++;
            }

            // Clear all KPI caches
            Cache::forget('kpi_inventory_value');
            Cache::forget('kpi_monthly_revenue');
            Cache::forget('kpi_gross_profit');
            Cache::forget('kpi_net_profit');

            Log::info("Bulk update completed: {$processedOrders} orders, {$totalItemsUpdated} items updated");

            return response()->json([
                'success' => true,
                'total_orders' => $totalOrders,
                'processed_orders' => $processedOrders,
                'total_items_updated' => $totalItemsUpdated,
                'month' => $month,
                'message' => "Successfully updated {$totalItemsUpdated} items across {$processedOrders} orders"
            ]);
        } catch (Exception $e) {
            Log::error('Error in bulk update profit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating profits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview profit updates for bulk processing
     */
    public function bulkPreviewProfit(Request $request)
    {
        try {
            $monthInput = $request->input('month');

            if (!$monthInput) {
                return response()->json([
                    'success' => false,
                    'message' => 'Month is required'
                ], 400);
            }

            // Parse the month
            $date = \Carbon\Carbon::parse($monthInput . '-01');
            $startDate = $date->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();

            Log::info('Bulk preview profit for period: ' . $startDate . ' to ' . $endDate);

            $shopId = auth()->user()->shop_id;

            // Get all orders for this month
            $orders = Order::where('shop_id', $shopId)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->with(['customer', 'details.product'])
                ->orderBy('created_at', 'desc')
                ->get();

            $ordersWithChanges = [];

            foreach ($orders as $order) {
                $currentProfit = 0;
                $newProfit = 0;
                $itemsToUpdate = 0;

                foreach ($order->details as $detail) {
                    // Calculate current profit (using stored buying_price)
                    $currentItemProfit = ($detail->unitcost - $detail->buying_price) * $detail->quantity;
                    $currentProfit += $currentItemProfit;

                    // Get current product buying price
                    $product = $detail->product;
                    if ($product) {
                        $product->refresh(); // Bypass any cache
                        $currentBuyingPrice = $product->buyingPrice ?? $detail->buying_price;

                        // Calculate new profit (using current product buying price)
                        $newItemProfit = ($detail->unitcost - $currentBuyingPrice) * $detail->quantity;
                        $newProfit += $newItemProfit;

                        // Check if buying price would change
                        if (abs($detail->buying_price - $currentBuyingPrice) > 0.01) {
                            $itemsToUpdate++;
                        }
                    } else {
                        // If no product, use stored buying price
                        $newProfit += $currentItemProfit;
                    }
                }

                // Only include orders that have changes
                if ($itemsToUpdate > 0) {
                    $ordersWithChanges[] = [
                        'id' => $order->id,
                        'invoice_number' => $order->invoice_no,
                        'date' => $order->created_at->format('Y-m-d'),
                        'customer_name' => $order->customer->name ?? 'Unknown',
                        'sale_amount' => $order->total,
                        'current_profit' => round($currentProfit, 2),
                        'new_profit' => round($newProfit, 2),
                        'items_to_update' => $itemsToUpdate
                    ];
                }
            }

            Log::info('Found ' . count($ordersWithChanges) . ' orders with changes');

            return response()->json([
                'success' => true,
                'orders' => $ordersWithChanges,
                'total_orders' => count($orders),
                'orders_with_changes' => count($ordersWithChanges)
            ]);
        } catch (Exception $e) {
            Log::error('Error in bulk preview profit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update selected orders' profit calculations
     */
    public function updateSelectedProfit(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids', []);

            if (empty($orderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders selected'
                ], 400);
            }

            Log::info('Updating selected orders: ' . implode(', ', $orderIds));

            $ordersUpdated = 0;
            $totalItemsUpdated = 0;

            // Get the orders
            $orders = Order::whereIn('id', $orderIds)
                ->with(['details.product'])
                ->get();

            foreach ($orders as $order) {
                $orderHasUpdates = false;

                foreach ($order->details as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        // Refresh product to bypass cache
                        $product->refresh();
                        $currentBuyingPrice = $product->buyingPrice;

                        // Only update if buying price has changed
                        if (abs($detail->buying_price - $currentBuyingPrice) > 0.01) {
                            // Use direct DB query to bypass Eloquent cache
                            DB::table('order_details')
                                ->where('id', $detail->id)
                                ->update([
                                    'buying_price' => $currentBuyingPrice,
                                    'updated_at' => now()
                                ]);

                            Log::info("Updated order_detail {$detail->id}: buying_price from {$detail->buying_price} to {$currentBuyingPrice}");

                            $totalItemsUpdated++;
                            $orderHasUpdates = true;
                        }
                    }
                }

                if ($orderHasUpdates) {
                    $ordersUpdated++;
                }
            }

            // Clear KPI caches
            Cache::forget('kpi_inventory_value');
            Cache::forget('kpi_monthly_revenue');
            Cache::forget('kpi_gross_profit');
            Cache::forget('kpi_net_profit');

            Log::info("Updated {$totalItemsUpdated} items across {$ordersUpdated} orders");

            return response()->json([
                'success' => true,
                'orders_updated' => $ordersUpdated,
                'total_items_updated' => $totalItemsUpdated,
                'message' => "Successfully updated {$totalItemsUpdated} items across {$ordersUpdated} orders"
            ]);
        } catch (Exception $e) {
            Log::error('Error updating selected profits: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating orders: ' . $e->getMessage()
            ], 500);
        }
    }

}

