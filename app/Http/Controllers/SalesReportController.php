<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\BusinessTransaction;
use App\Models\Expense;
use App\Models\Delivery;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    private const ACTIVITY_LOG_PER_PAGE = 2;

    private function reportView(string $view): string
    {
        $shopType = function_exists('active_shop_type') ? active_shop_type() : 'tech';
        $shopView = "shop-types.{$shopType}.reports.{$view}";

        return view()->exists($shopView)
            ? $shopView
            : "reports.{$view}";
    }

    public function index()
    {
        // Get summary data for the dashboard
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $dailySales = $this->getDailySalesData($today);
        $weeklySales = $this->getWeeklySalesData($thisWeek);
        $monthlySales = $this->getMonthlySalesData($thisMonth);

        // Get top selling products for this month
        $topProducts = $this->getTopSellingProducts($thisMonth);

        // Get recent orders
        $recentOrders = Order::with(['customer', 'details.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view($this->reportView('sales.index'), compact(
            'dailySales',
            'weeklySales',
            'monthlySales',
            'topProducts',
            'recentOrders'
        ));
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $salesData = $this->getDailySalesData($selectedDate);
        $hourlyData = $this->getHourlySalesData($selectedDate);
        $salesDetailsData = $this->getDailySalesDetails($selectedDate);
        $paymentMethodData = $this->getPaymentMethodBreakdown($selectedDate);
        $expensesData = $this->getDailyExpenses($selectedDate);
        $purchasesData = $this->getDailyPurchases($selectedDate);
        $transactionsData = $this->getDailyTransactions($selectedDate);
        $deliveriesData = $this->getDailyDeliveries($selectedDate);
        $activitiesData = $this->getDailyActivities($selectedDate);
        $dailySummary = $this->getDailyOperationsSummary($selectedDate);

        return view($this->reportView('sales.daily'), compact('salesData', 'hourlyData', 'salesDetailsData', 'paymentMethodData', 'selectedDate', 'expensesData', 'purchasesData', 'transactionsData', 'deliveriesData', 'activitiesData', 'dailySummary'));
    }

    public function weekly(Request $request)
    {
        $week = $request->get('week', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $selectedWeek = Carbon::parse($week)->startOfWeek();

        $salesData = $this->getWeeklySalesData($selectedWeek);
        $dailyData = $this->getDailyDataForWeek($selectedWeek);
        $expensesData = $this->getWeeklyExpenses($selectedWeek);
        $transactionsData = $this->getWeeklyTransactions($selectedWeek);
        $deliveriesData = $this->getWeeklyDeliveries($selectedWeek);
        $activitiesData = $this->getWeeklyActivities($selectedWeek);

        return view($this->reportView('sales.weekly'), compact(
            'salesData',
            'dailyData',
            'selectedWeek',
            'expensesData',
            'transactionsData',
            'deliveriesData',
            'activitiesData'
        ));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        $salesData = $this->getMonthlySalesData($selectedMonth);
        $dailyData = $this->getDailyDataForMonth($selectedMonth);
        $weeklyData = $this->getWeeklyDataForMonth($selectedMonth);
    $expensesData = $this->getMonthlyExpenses($selectedMonth);
    $transactionsData = $this->getMonthlyTransactions($selectedMonth);
    $transactionsSummary = $this->getMonthlyTransactionSummary($selectedMonth);
    $deliveriesData = $this->getMonthlyDeliveries($selectedMonth);
    $activitiesData = $this->getMonthlyActivities($selectedMonth);

    return view($this->reportView('sales.monthly'), compact('salesData', 'dailyData', 'weeklyData', 'selectedMonth', 'expensesData', 'transactionsData', 'transactionsSummary', 'deliveriesData', 'activitiesData'));
    }

    public function yearly(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $selectedYear = intval($year);
        $selectedYearDate = Carbon::parse($year . '-01-01');

        $salesData = $this->getYearlySalesData($selectedYearDate);
        $monthlyData = $this->getMonthlyDataForYear($selectedYearDate);
        $quarterlyData = $this->getQuarterlyDataForYear($selectedYearDate);
        $previousYearData = $this->getYearlySalesData(Carbon::parse(($selectedYear - 1) . '-01-01'));

        $expensesData = $this->getYearlyExpenses($selectedYearDate);
        $expensesGrouped = $this->getAllYearlyExpenses($selectedYearDate);
        $transactionsData = $this->getYearlyTransactions($selectedYearDate);
        $transactionsSummary = $this->getYearlyTransactionSummary($selectedYearDate);
        $deliveriesData = $this->getYearlyDeliveries($selectedYearDate);
        $activitiesData = $this->getYearlyActivities($selectedYearDate);

        // Get total delivery cost for financial calculations (not paginated)
        $totalDeliveryCost = $this->getAllYearlyDeliveries($selectedYearDate)->sum('cost');

        // Calculate comprehensive financial metrics
        $financialMetrics = $this->calculateYearlyFinancialMetrics($selectedYearDate, $salesData, $expensesGrouped, $totalDeliveryCost);

        return view($this->reportView('sales.yearly'), compact(
            'salesData',
            'monthlyData',
            'quarterlyData',
            'previousYearData',
            'selectedYear',
            'expensesData',
            'expensesGrouped',
            'transactionsData',
            'transactionsSummary',
            'deliveriesData',
            'activitiesData',
            'financialMetrics',
            'totalDeliveryCost'
        ));
    }

    public function downloadDaily(Request $request)
    {
        // Authorization check
        if (!auth()->user()->canAccessReports()) {
            abort(403, 'You do not have permission to download reports.');
        }

        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $sectionKeys = [
            'includeSummary',
            'includePayments',
            'includeExpenses',
            'includePurchases',
            'includeTransactions',
            'includeDeliveries',
            'includeActivities',
        ];

        $hasSectionSelection = $request->hasAny($sectionKeys);

        $sections = [
            'includeHeader' => true,
            'includeSummary' => $hasSectionSelection ? $request->boolean('includeSummary') : true,
            'includePayments' => $hasSectionSelection ? $request->boolean('includePayments') : true,
            'includeExpenses' => $hasSectionSelection ? $request->boolean('includeExpenses') : true,
            'includePurchases' => $hasSectionSelection ? $request->boolean('includePurchases') : true,
            'includeTransactions' => $hasSectionSelection ? $request->boolean('includeTransactions') : true,
            'includeDeliveries' => $hasSectionSelection ? $request->boolean('includeDeliveries') : true,
            'includeActivities' => $hasSectionSelection ? $request->boolean('includeActivities') : false,
        ];

        if (!collect($sections)->contains(true)) {
            return back()->with('error', 'Please select at least one section to include.');
        }

        $salesData = $sections['includeSummary'] ? $this->getDailySalesData($selectedDate->copy()) : null;
        $salesOrdersData = $this->getAllDailySalesOrders($selectedDate->copy());
        $paymentMethodData = $sections['includePayments'] ? $this->getPaymentMethodBreakdown($selectedDate->copy()) : collect();
        $expensesData = $sections['includeExpenses'] ? $this->getDailyExpenses($selectedDate->copy()) : collect();
        $purchasesData = $sections['includePurchases'] ? $this->getAllDailyPurchases($selectedDate->copy()) : collect();
        $transactionsData = $sections['includeTransactions'] ? $this->getAllDailyTransactions($selectedDate->copy()) : collect();
        $deliveriesData = $sections['includeDeliveries'] ? $this->getAllDailyDeliveries($selectedDate->copy()) : collect();
        $activitiesData = $sections['includeActivities'] ? $this->getAllDailyActivities($selectedDate->copy()) : collect();

        $shop = auth()->user()->getActiveShop() ?? auth()->user()->shop;

        $pdf = Pdf::loadView($this->reportView('sales.daily-pdf'), [
            'selectedDate' => $selectedDate,
            'shop' => $shop,
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now(),
            'sections' => $sections,
            'salesData' => $salesData,
            'salesOrdersData' => $salesOrdersData,
            'paymentMethodData' => $paymentMethodData,
            'expensesData' => $expensesData,
            'purchasesData' => $purchasesData,
            'transactionsData' => $transactionsData,
            'deliveriesData' => $deliveriesData,
            'activitiesData' => $activitiesData,
        ])->setPaper('a4', 'portrait');

        $filename = 'daily_sales_report_' . $selectedDate->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadWeekly(Request $request)
    {
        if (!auth()->user()->canAccessReports()) {
            abort(403, 'You do not have permission to download reports.');
        }

        $week = $request->get('week', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $selectedWeek = Carbon::parse($week)->startOfWeek();

        $sectionKeys = [
            'includeSummary',
            'includeDailyBreakdown',
            'includeExpenses',
            'includeTransactions',
            'includeDeliveries',
            'includeActivities',
        ];

        $hasSectionSelection = $request->hasAny($sectionKeys);

        $sections = [
            'includeHeader' => true,
            'includeSummary' => $hasSectionSelection ? $request->boolean('includeSummary') : true,
            'includeDailyBreakdown' => $hasSectionSelection ? $request->boolean('includeDailyBreakdown') : true,
            'includeExpenses' => $hasSectionSelection ? $request->boolean('includeExpenses') : true,
            'includeTransactions' => $hasSectionSelection ? $request->boolean('includeTransactions') : true,
            'includeDeliveries' => $hasSectionSelection ? $request->boolean('includeDeliveries') : true,
            'includeActivities' => $hasSectionSelection ? $request->boolean('includeActivities') : false,
        ];

        if (!collect($sections)->contains(true)) {
            return back()->with('error', 'Please select at least one section to include.');
        }

        $salesData = $sections['includeSummary'] ? $this->getWeeklySalesData($selectedWeek->copy()) : null;
        $dailyData = $sections['includeDailyBreakdown'] ? $this->getDailyDataForWeek($selectedWeek->copy()) : collect();
        $expensesData = $sections['includeExpenses'] ? $this->getWeeklyExpenses($selectedWeek->copy()) : collect();
        $transactionsData = $sections['includeTransactions'] ? $this->getAllWeeklyTransactions($selectedWeek->copy()) : collect();
        $deliveriesData = $sections['includeDeliveries'] ? $this->getAllWeeklyDeliveries($selectedWeek->copy()) : collect();
        $activitiesData = $sections['includeActivities'] ? $this->getAllWeeklyActivities($selectedWeek->copy()) : collect();

        $shop = auth()->user()->getActiveShop() ?? auth()->user()->shop;

        $pdf = Pdf::loadView($this->reportView('sales.weekly-pdf'), [
            'selectedWeek' => $selectedWeek,
            'shop' => $shop,
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now(),
            'sections' => $sections,
            'salesData' => $salesData,
            'dailyData' => $dailyData,
            'expensesData' => $expensesData,
            'transactionsData' => $transactionsData,
            'deliveriesData' => $deliveriesData,
            'activitiesData' => $activitiesData,
        ])->setPaper('a4', 'portrait');

        $filename = 'weekly_sales_report_' . $selectedWeek->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadMonthly(Request $request)
    {
        if (!auth()->user()->canAccessReports()) {
            abort(403, 'You do not have permission to download reports.');
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        $sectionKeys = [
            'includeSummary',
            'includeTopDays',
            'includeStatistics',
            'includeExpenses',
            'includeTransactions',
            'includeTransactionSummary',
            'includeDeliveries',
            'includeActivities',
        ];

        $hasSectionSelection = $request->hasAny($sectionKeys);

        $sections = [
            'includeHeader' => true,
            'includeSummary' => $hasSectionSelection ? $request->boolean('includeSummary') : true,
            'includeTopDays' => $hasSectionSelection ? $request->boolean('includeTopDays') : true,
            'includeStatistics' => $hasSectionSelection ? $request->boolean('includeStatistics') : true,
            'includeExpenses' => $hasSectionSelection ? $request->boolean('includeExpenses') : true,
            'includeTransactions' => $hasSectionSelection ? $request->boolean('includeTransactions') : true,
            'includeTransactionSummary' => $hasSectionSelection ? $request->boolean('includeTransactionSummary') : true,
            'includeDeliveries' => $hasSectionSelection ? $request->boolean('includeDeliveries') : true,
            'includeActivities' => $hasSectionSelection ? $request->boolean('includeActivities') : false,
        ];

        if (!collect($sections)->contains(true)) {
            return back()->with('error', 'Please select at least one section to include.');
        }

        $salesData = $sections['includeSummary'] ? $this->getMonthlySalesData($selectedMonth->copy()) : null;
        $dailyData = $this->getDailyDataForMonth($selectedMonth->copy());
        $expensesData = $sections['includeExpenses'] ? $this->getMonthlyExpenses($selectedMonth->copy()) : collect();
        $transactionsData = $sections['includeTransactions'] ? $this->getAllMonthlyTransactions($selectedMonth->copy()) : collect();
        $transactionsSummary = $sections['includeTransactionSummary'] ? $this->getMonthlyTransactionSummary($selectedMonth->copy()) : collect();
        $deliveriesData = $sections['includeDeliveries'] ? $this->getAllMonthlyDeliveries($selectedMonth->copy()) : collect();
        $activitiesData = $sections['includeActivities'] ? $this->getAllMonthlyActivities($selectedMonth->copy()) : collect();

        $shop = auth()->user()->getActiveShop() ?? auth()->user()->shop;

        $pdf = Pdf::loadView($this->reportView('sales.monthly-pdf'), [
            'selectedMonth' => $selectedMonth,
            'shop' => $shop,
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now(),
            'sections' => $sections,
            'salesData' => $salesData,
            'dailyData' => $dailyData,
            'expensesData' => $expensesData,
            'transactionsData' => $transactionsData,
            'transactionsSummary' => $transactionsSummary,
            'deliveriesData' => $deliveriesData,
            'activitiesData' => $activitiesData,
        ])->setPaper('a4', 'portrait');

        $filename = 'monthly_sales_report_' . $selectedMonth->format('Y_m') . '.pdf';
        return $pdf->download($filename);
    }

    // API endpoints for AJAX requests
    public function apiDaily(Request $request)
    {
        $date = Carbon::parse($request->get('date', Carbon::today()));
        $data = $this->getDailySalesData($date);

        return response()->json($data);
    }

    public function apiWeekly(Request $request)
    {
        $week = Carbon::parse($request->get('week', Carbon::now()))->startOfWeek();
        $data = $this->getWeeklySalesData($week);

        return response()->json($data);
    }

    public function apiMonthly(Request $request)
    {
        $month = Carbon::parse($request->get('month', Carbon::now()))->startOfMonth();
        $data = $this->getMonthlySalesData($month);

        return response()->json($data);
    }

    // Private helper methods
    private function getDailyOperationsSummary($date)
    {
        $totalPurchases = BusinessTransaction::whereDate('transaction_date', $date)
            ->where('transaction_type', 'purchase')
            ->sum('total_amount');
        $purchaseCount = BusinessTransaction::whereDate('transaction_date', $date)
            ->where('transaction_type', 'purchase')
            ->count();

        $transactionCount = BusinessTransaction::whereDate('transaction_date', $date)->count();
        $transactionAmount = BusinessTransaction::whereDate('transaction_date', $date)->sum('total_amount');

        $deliveryCount = Delivery::whereDate('delivery_date', $date)->count();
        $deliveryCost = Delivery::whereDate('delivery_date', $date)->sum('cost');

        return [
            'total_purchases' => $totalPurchases,
            'purchase_count' => $purchaseCount,
            'transaction_count' => $transactionCount,
            'transaction_amount' => $transactionAmount,
            'delivery_count' => $deliveryCount,
            'delivery_cost' => $deliveryCost,
        ];
    }

    private function getDailySalesData($date)
    {
        // Use DB aggregates to avoid loading full order collections
        $totalSales = Order::whereDate('order_date', $date)->sum('total');
        $totalOrders = Order::whereDate('order_date', $date)->count();

        $totalItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereDate('orders.order_date', $date)
            ->sum('order_details.quantity');

        $grossProfit = $this->computeGrossProfitForRange(
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        );

        return [
            'date' => $date->format('Y-m-d'),
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'total_items_sold' => $totalItems,
            'gross_profit' => $grossProfit,
        ];
    }

    private function getWeeklySalesData($startOfWeek)
    {
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
        $totalSales = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->sum('total');
        $totalOrders = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->count();

        $totalItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$startOfWeek, $endOfWeek])
            ->sum('order_details.quantity');

        $grossProfit = $this->computeGrossProfitForRange($startOfWeek->startOfDay(), $endOfWeek->endOfDay());

        return [
            'week_start' => $startOfWeek->format('Y-m-d'),
            'week_end' => $endOfWeek->format('Y-m-d'),
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'total_items_sold' => $totalItems,
            'gross_profit' => $grossProfit,
        ];
    }

    private function getMonthlySalesData($startOfMonth)
    {
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $totalSales = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->sum('total');
        $totalOrders = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->count();

        $totalItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$startOfMonth, $endOfMonth])
            ->sum('order_details.quantity');

        $grossProfit = $this->computeGrossProfitForRange($startOfMonth->startOfDay(), $endOfMonth->endOfDay());

        return [
            'month' => $startOfMonth->format('Y-m'),
            'month_name' => $startOfMonth->format('F Y'),
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'total_items_sold' => $totalItems,
            'gross_profit' => $grossProfit,
        ];
    }

    private function getYearlySalesData($year)
    {
        $startOfYear = $year->copy()->startOfYear();
        $endOfYear = $year->copy()->endOfYear();
        $totalSales = Order::whereBetween('order_date', [$startOfYear, $endOfYear])->sum('total');
        $totalOrders = Order::whereBetween('order_date', [$startOfYear, $endOfYear])->count();

        $totalItems = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$startOfYear, $endOfYear])
            ->sum('order_details.quantity');

        $grossProfit = $this->computeGrossProfitForRange($startOfYear->startOfDay(), $endOfYear->endOfDay());

        return [
            'year' => $year->year,
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'total_items_sold' => $totalItems,
            'gross_profit' => $grossProfit,
        ];
    }

    private function getHourlySalesData($date)
    {
        return Order::whereDate('order_date', $date)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');
    }

    /**
     * Get payment method breakdown for a date
     */
    private function getPaymentMethodBreakdown($date)
    {
        return Order::whereDate('order_date', $date)
            ->select(
                'payment_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('payment_type')
            ->orderBy('payment_type')
            ->get()
            ->keyBy('payment_type');
    }

    private function getDailyExpenses($date)
    {
        return Expense::whereDate('expense_date', $date)
            ->select(
                'id',
                'type',
                'amount',
                'notes',
                'expense_date'
            )
            ->orderBy('expense_date', 'desc')
            ->get()
            ->groupBy('type');
    }

    private function getDailySalesDetails($date)
    {
        return Order::whereDate('order_date', $date)
            ->select(
                'id',
                'order_date',
                'invoice_no',
                'customer_id',
                'payment_type',
                'total',
                'due'
            )
            ->with('customer:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sales_page');
    }

    private function getDailyTransactions($date)
    {
        return BusinessTransaction::whereDate('transaction_date', $date)
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
    }

    private function getDailyPurchases($date)
    {
        return BusinessTransaction::whereDate('transaction_date', $date)
            ->where('transaction_type', 'purchase')
            ->select(
                'id',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
    }

    private function getAllDailySalesOrders($date)
    {
        return Order::whereDate('order_date', $date)
            ->select('id', 'order_date', 'invoice_no', 'customer_id', 'total', 'due', 'payment_type')
            ->with('customer:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getAllDailyPurchases($date)
    {
        return BusinessTransaction::whereDate('transaction_date', $date)
            ->where('transaction_type', 'purchase')
            ->select(
                'id',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    private function getAllDailyTransactions($date)
    {
        return BusinessTransaction::whereDate('transaction_date', $date)
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    private function getDailyDeliveries($date)
    {
        return Delivery::whereDate('delivery_date', $date)
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->paginate(10);
    }

    private function getAllDailyDeliveries($date)
    {
        return Delivery::whereDate('delivery_date', $date)
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->get();
    }

    private function getDailyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;

        return AuditLog::where('shop_id', $shopId)
            ->whereDate('created_at', $date)
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
                ->paginate(self::ACTIVITY_LOG_PER_PAGE);
    }

    private function getAllDailyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;

        return AuditLog::where('shop_id', $shopId)
            ->whereDate('created_at', $date)
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Weekly data methods
    private function getWeeklyExpenses($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return Expense::whereBetween('expense_date', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'type',
                'amount',
                'notes',
                'expense_date'
            )
            ->orderBy('expense_date', 'desc')
            ->get()
            ->groupBy('type');
    }

    private function getWeeklyTransactions($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
    }

    private function getAllWeeklyTransactions($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    private function getWeeklyDeliveries($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return Delivery::whereBetween('delivery_date', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->paginate(10);
    }

    private function getAllWeeklyDeliveries($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return Delivery::whereBetween('delivery_date', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->get();
    }

    private function getWeeklyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
                ->paginate(self::ACTIVITY_LOG_PER_PAGE);
    }

    private function getAllWeeklyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Monthly data methods
    private function getMonthlyExpenses($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'type',
                'amount',
                'notes',
                'expense_date'
            )
            ->orderBy('expense_date', 'desc')
            ->get()
            ->groupBy('type');
    }

    private function getMonthlyTransactions($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
    }

    private function getAllMonthlyTransactions($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    private function getMonthlyDeliveries($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->paginate(10);
    }

    private function getAllMonthlyDeliveries($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Delivery::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->get();
    }

    private function getMonthlyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
                ->paginate(self::ACTIVITY_LOG_PER_PAGE);
    }

    private function getAllMonthlyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getMonthlyTransactionSummary($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->select(
                'category',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('category')
            ->orderBy('category')
            ->get();
    }

    // Yearly data methods
    private function getYearlyExpenses($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        $expenses = Expense::whereBetween('expense_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'type',
                'amount',
                'notes',
                'expense_date'
            )
            ->orderBy('type')
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        return $expenses;
    }

    private function getAllYearlyExpenses($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return Expense::whereBetween('expense_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'type',
                'amount',
                'notes',
                'expense_date'
            )
            ->orderBy('expense_date', 'desc')
            ->get()
            ->groupBy('type');
    }

    private function getYearlyTransactions($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
    }

    private function getAllYearlyTransactions($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'transaction_type',
                'vendor_name',
                'total_amount',
                'status',
                'category',
                'transaction_date'
            )
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    private function getYearlyDeliveries($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return Delivery::whereBetween('delivery_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->paginate(10);
    }

    private function getAllYearlyDeliveries($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return Delivery::whereBetween('delivery_date', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'tracking_number',
                'from_location',
                'to_location',
                'delivery_date',
                'cost',
                'notes'
            )
            ->orderBy('delivery_date', 'desc')
            ->get();
    }

    private function getYearlyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
                ->paginate(self::ACTIVITY_LOG_PER_PAGE);
    }

    private function getAllYearlyActivities($date)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return AuditLog::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->select(
                'id',
                'user_id',
                'action',
                'model_type',
                'model_id',
                'description',
                'old_data',
                'new_data',
                'created_at'
            )
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getYearlyTransactionSummary($date)
    {
        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();

        return BusinessTransaction::whereBetween('transaction_date', [$startOfYear, $endOfYear])
            ->select(
                'category',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('category')
            ->orderBy('category')
            ->get();
    }

    private function getDailyDataForWeek($startOfWeek)
    {
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])
            ->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');
    }

    private function getDailyDataForMonth($startOfMonth)
    {
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])
            ->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');
    }

    private function getWeeklyDataForMonth($startOfMonth)
    {
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])
            ->select(DB::raw('WEEK(order_date) as week'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('WEEK(order_date)'))
            ->orderBy('week')
            ->get()
            ->keyBy('week');
    }

    private function getMonthlyDataForYear($year)
    {
        $startOfYear = $year->copy()->startOfYear();
        $endOfYear = $year->copy()->endOfYear();

        return Order::whereBetween('order_date', [$startOfYear, $endOfYear])
            ->select(DB::raw('MONTH(order_date) as month'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('MONTH(order_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
    }

    private function getQuarterlyDataForYear($year)
    {
        $startOfYear = $year->copy()->startOfYear();
        $endOfYear = $year->copy()->endOfYear();

        return Order::whereBetween('order_date', [$startOfYear, $endOfYear])
            ->select(DB::raw('QUARTER(order_date) as quarter'), DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->groupBy(DB::raw('QUARTER(order_date)'))
            ->orderBy('quarter')
            ->get()
            ->keyBy('quarter');
    }

    private function getTopSellingProducts($startDate, $limit = 10)
    {
        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.order_date', '>=', $startDate)
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.total) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Compute gross profit for orders between two datetimes.
     *
     * Use the same per-line profit logic as the finance flows:
     * (selling unit price - buying price) * quantity.
     * 
     * Note: This automatically handles losses where selling_price < buying_price.
     * - When selling_price > buying_price: contributes POSITIVE to gross profit
     * - When selling_price < buying_price: contributes NEGATIVE to gross profit (loss)
     * The total is the sum of all profits minus all losses.
     */
    private function computeGrossProfitForRange($startDateTime, $endDateTime)
    {
        $shopId = auth()->user()?->getActiveShop()?->id;

        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->whereBetween('orders.order_date', [$startDateTime, $endDateTime]);

        if ($shopId) {
            $query->where('orders.shop_id', $shopId);
        }

        return (float) ($query
            ->selectRaw('SUM((COALESCE(order_details.unitcost, 0) - COALESCE(order_details.buying_price, products.buying_price, 0)) * COALESCE(order_details.quantity, 0)) as gross_profit')
            ->value('gross_profit') ?? 0);
    }

    /**
     * Calculate comprehensive financial metrics for yearly report
     */
    private function calculateYearlyFinancialMetrics($yearDate, $salesData, $expensesData, $totalDeliveryCost)
    {
        $shopId = auth()->user()->getActiveShop()->id ?? null;
        $startOfYear = $yearDate->copy()->startOfYear();
        $endOfYear = $yearDate->copy()->endOfYear();

        // Revenue Metrics
        $totalRevenue = $salesData['total_sales'];
        $totalOrders = $salesData['total_orders'];

        // Calculate COGS (Cost of Goods Sold)
        $cogs = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.order_date', [$startOfYear, $endOfYear])
            ->selectRaw('SUM(order_details.quantity * COALESCE(order_details.buying_price, products.buying_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        // Gross Profit (already calculated)
        $grossProfit = $salesData['gross_profit'];

        // Operating Expenses
        $totalExpenses = $expensesData->flatMap(fn($items) => $items)->sum('amount');

        // Delivery Costs
        $deliveryCosts = $totalDeliveryCost;

        // Net Profit Calculation
        $operatingExpenses = $totalExpenses + $deliveryCosts;
        $operatingProfit = $grossProfit - $totalExpenses;
        $netProfit = $grossProfit - $operatingExpenses;

        // Calculate Tax (if applicable - 0 for now, can be added later)
        $taxAmount = 0;
        $netProfitAfterTax = $netProfit - $taxAmount;

        // Financial Margins & Ratios
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $operatingMargin = $totalRevenue > 0 ? ($operatingProfit / $totalRevenue) * 100 : 0;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Cost Ratios
        $cogsPercentage = $totalRevenue > 0 ? ($cogs / $totalRevenue) * 100 : 0;
        $expenseRatio = $totalRevenue > 0 ? ($operatingExpenses / $totalRevenue) * 100 : 0;

        // Per Order Metrics
        $avgRevenuePerOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $avgProfitPerOrder = $totalOrders > 0 ? $netProfit / $totalOrders : 0;
        $avgCostPerOrder = $totalOrders > 0 ? ($cogs + $operatingExpenses) / $totalOrders : 0;

        // Break-even Analysis
        $totalCosts = $cogs + $operatingExpenses;
        $contributionMargin = $totalRevenue - $cogs;
        $contributionMarginRatio = $totalRevenue > 0 ? ($contributionMargin / $totalRevenue) * 100 : 0;

        // Expense Breakdown by Type
        $expensesByType = [];
        foreach ($expensesData as $expenseType => $expenses) {
            $typeTotal = $expenses->sum('amount');
            $expensesByType[$expenseType] = [
                'amount' => $typeTotal,
                'percentage' => $totalExpenses > 0 ? ($typeTotal / $totalExpenses) * 100 : 0
            ];
        }

        // Monthly averages
        $avgMonthlyRevenue = $totalRevenue / 12;
        $avgMonthlyProfit = $netProfit / 12;
        $avgMonthlyExpenses = $operatingExpenses / 12;
        $avgMonthlyOrders = $totalOrders / 12;

        // Return on Sales
        $returnOnSales = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Loss/Profit indicator
        $isProfitable = $netProfit > 0;
        $profitLossStatus = $netProfit > 0 ? 'Profit' : ($netProfit < 0 ? 'Loss' : 'Break-even');

        return [
            // Core Financial Metrics
            'total_revenue' => $totalRevenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'total_expenses' => $totalExpenses,
            'delivery_costs' => $deliveryCosts,
            'operating_expenses' => $operatingExpenses,
            'operating_profit' => $operatingProfit,
            'net_profit' => $netProfit,
            'tax_amount' => $taxAmount,
            'net_profit_after_tax' => $netProfitAfterTax,

            // Margins & Ratios
            'gross_margin' => $grossMargin,
            'operating_margin' => $operatingMargin,
            'net_margin' => $netMargin,
            'cogs_percentage' => $cogsPercentage,
            'expense_ratio' => $expenseRatio,
            'contribution_margin_ratio' => $contributionMarginRatio,
            'return_on_sales' => $returnOnSales,

            // Per Order Metrics
            'avg_revenue_per_order' => $avgRevenuePerOrder,
            'avg_profit_per_order' => $avgProfitPerOrder,
            'avg_cost_per_order' => $avgCostPerOrder,

            // Break-even & Cost Analysis
            'total_costs' => $totalCosts,
            'contribution_margin' => $contributionMargin,
            'expenses_by_type' => $expensesByType,

            // Monthly Averages
            'avg_monthly_revenue' => $avgMonthlyRevenue,
            'avg_monthly_profit' => $avgMonthlyProfit,
            'avg_monthly_expenses' => $avgMonthlyExpenses,
            'avg_monthly_orders' => $avgMonthlyOrders,

            // Status Indicators
            'is_profitable' => $isProfitable,
            'profit_loss_status' => $profitLossStatus,
        ];
    }

    /**
     * Show main reports index page
     */
    public function reportsIndex()
    {
        // Authorization check - staff cannot access reports
        if (!auth()->user()->canAccessReports()) {
            abort(403, 'You do not have permission to access Reports.');
        }

        return view($this->reportView('index'));
    }

    /**
     * Business Transactions Report
     */
    public function transactions(Request $request)
    {
        // Authorization check - staff cannot access transactions
        if (!auth()->user()->canAccessTransactions()) {
            abort(403, 'You do not have permission to access Transactions.');
        }

        $activeShop = auth()->user()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();

        // Get transactions for the selected month
        $transactions = BusinessTransaction::where('shop_id', $activeShop->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Calculate summary statistics
        $totalAmount = $transactions->sum('net_amount');
        $totalTransactions = $transactions->count();

        $byCategory = $transactions->groupBy('category')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('net_amount')
            ];
        });

        $byPaymentMethod = $transactions->groupBy('paid_by')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('net_amount')
            ];
        });

        return view($this->reportView('transactions'), compact(
            'transactions',
            'selectedMonth',
            'totalAmount',
            'totalTransactions',
            'byCategory',
            'byPaymentMethod'
        ));
    }

    /**
     * Download Business Transactions Report as CSV
     */
    public function downloadTransactions(Request $request)
    {
        // Authorization check - staff cannot download transactions
        if (!auth()->user()->canAccessTransactions()) {
            abort(403, 'You do not have permission to download Transactions.');
        }

        $activeShop = auth()->user()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();

        $transactions = BusinessTransaction::where('shop_id', $activeShop->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'business_transactions_' . $selectedMonth->format('Y_m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Date',
                'Type',
                'Vendor',
                'Receipt Number',
                'Reference Number',
                'Category',
                'Payment Method',
                'Paid By User',
                'Total Amount',
                'Discount',
                'Net Amount',
                'Status',
                'Description'
            ]);

            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d H:i'),
                    $transaction->formatted_type,
                    $transaction->vendor_name ?? '',
                    $transaction->receipt_number ?? '',
                    $transaction->reference_number ?? '',
                    $transaction->category ?? '',
                    $transaction->formatted_paid_by,
                    $transaction->paidByUser->name ?? '',
                    number_format($transaction->total_amount, 2),
                    number_format($transaction->discount_amount, 2),
                    number_format($transaction->net_amount, 2),
                    ucfirst($transaction->status),
                    $transaction->description ?? ''
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Inventory Report
     */
    public function inventory(Request $request)
    {
        $activeShop = auth()->user()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $category = $request->get('category');
        $search = $request->get('search');
        $stockFilter = $request->get('stock_status');

        $productsQuery = Product::where('shop_id', $activeShop->id);

        if ($category) {
            if ($category === 'uncategorized') {
                $productsQuery->whereNull('category_id');
            } else {
                $productsQuery->where('category_id', $category);
            }
        }

        if ($search) {
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Apply stock status filter
        if ($stockFilter) {
            if ($stockFilter === 'out_of_stock') {
                $productsQuery->where('quantity', '<=', 0);
            } elseif ($stockFilter === 'low_stock') {
                $productsQuery->where('quantity', '>', 0)
                    ->where('quantity', '<=', 10);
            } elseif ($stockFilter === 'in_stock') {
                $productsQuery->where('quantity', '>', 10);
            }
        }

        // Sort products: Out of stock first, then low stock, then in stock, then by name
        $productsQuery->orderByRaw('
            CASE
                WHEN quantity <= 0 THEN 0
                WHEN quantity <= 10 THEN 1
                ELSE 2
            END
        ')->orderBy('name', 'asc');

        // Paginate products (10 per page)
        $products = $productsQuery->with('category')->paginate(10)->appends(request()->query());

        // Calculate statistics for all products (not just the current page)
        $allProductsQuery = Product::where('shop_id', $activeShop->id);

        if ($category) {
            if ($category === 'uncategorized') {
                $allProductsQuery->whereNull('category_id');
            } else {
                $allProductsQuery->where('category_id', $category);
            }
        }

        if ($search) {
            $allProductsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($stockFilter) {
            if ($stockFilter === 'out_of_stock') {
                $allProductsQuery->where('quantity', '<=', 0);
            } elseif ($stockFilter === 'low_stock') {
                $allProductsQuery->where('quantity', '>', 0)
                    ->where('quantity', '<=', 10);
            } elseif ($stockFilter === 'in_stock') {
                $allProductsQuery->where('quantity', '>', 10);
            }
        }

        $totalProducts = $allProductsQuery->count();
        $totalStockValue = $allProductsQuery->sum(DB::raw('quantity * COALESCE(buying_price, 0)'));
        $lowStockProducts = Product::where('shop_id', $activeShop->id)
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', 10)->count();
        $outOfStockProducts = Product::where('shop_id', $activeShop->id)
            ->where('quantity', '<=', 0)
            ->count();

        // Get categories for filter
        $categories = DB::table('categories')
            ->where('shop_id', $activeShop->id)
            ->orderBy('name')
            ->get();

        return view($this->reportView('inventory'), compact(
            'products',
            'totalProducts',
            'totalStockValue',
            'lowStockProducts',
            'outOfStockProducts',
            'categories',
            'category',
            'search',
            'stockFilter'
        ));
    }

    /**
     * Download Inventory Report as CSV
     */
    public function downloadInventory(Request $request)
    {
        $activeShop = auth()->user()->getActiveShop();

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $category = $request->get('category');
        $search = $request->get('search');
        $stockFilter = $request->get('stock_status');

        $productsQuery = Product::where('shop_id', $activeShop->id);

        if ($category) {
            if ($category === 'uncategorized') {
                $productsQuery->whereNull('category_id');
            } else {
                $productsQuery->where('category_id', $category);
            }
        }

        if ($search) {
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Apply stock status filter
        if ($stockFilter) {
            if ($stockFilter === 'out_of_stock') {
                $productsQuery->where('quantity', '<=', 0);
            } elseif ($stockFilter === 'low_stock') {
                $productsQuery->where('quantity', '>', 0)
                    ->where('quantity', '<=', 10);
            } elseif ($stockFilter === 'in_stock') {
                $productsQuery->where('quantity', '>', 10);
            }
        }

        // Sort same as display: Out of stock first, then low stock, then in stock, then by name
        $productsQuery->orderByRaw('
            CASE
                WHEN quantity <= 0 THEN 0
                WHEN quantity <= 10 THEN 1
                ELSE 2
            END
        ')->orderBy('name', 'asc');

        $products = $productsQuery->with('category')->get();

        $filename = 'inventory_report_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Product Code',
                'Product Name',
                'Category',
                'Quantity',
                'Unit Price',
                'Buying Price',
                'Stock Value',
                'Status'
            ]);

            // Data rows
            foreach ($products as $product) {
                $status = 'In Stock';
                if ($product->quantity <= 0) {
                    $status = 'Out of Stock';
                } elseif ($product->quantity <= 10) {
                    $status = 'Low Stock';
                }

                fputcsv($file, [
                    $product->code,
                    $product->name,
                    $product->category->name ?? 'N/A',
                    $product->quantity,
                    number_format($product->price, 2),
                    number_format($product->buying_price ?? 0, 2),
                    number_format($product->quantity * $product->price, 2),
                    $status
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
