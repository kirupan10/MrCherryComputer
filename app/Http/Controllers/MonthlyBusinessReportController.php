<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Expense;
use App\Models\CreditPurchase;
use App\Models\CreditPurchasePayment;
use App\Models\Shop;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class MonthlyBusinessReportController extends Controller
{
    /**
     * Display monthly business report
     */
    public function index(Request $request)
    {
        // Get selected month or default to current month
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        // Get user's active shop
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        // Get report data for the shop
        $reportData = $this->generateShopReport($activeShop->id, $selectedMonth);

        return view('reports.business.monthly', compact('reportData', 'selectedMonth', 'activeShop'));
    }

    /**
     * Generate comprehensive report for a shop
     */
    private function generateShopReport($shopId, $month)
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        // 1. REVENUE CALCULATION (Sales)
        $salesRevenue = Order::where('shop_id', $shopId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total');

        $totalOrders = Order::where('shop_id', $shopId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->count();

        // 2. COST OF GOODS SOLD (COGS)
        // Use COALESCE to prefer order_details.buying_price, fallback to products.buying_price
        $cogs = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->sum(DB::raw('order_details.quantity * COALESCE(order_details.buying_price, products.buying_price, 0)'));

        // 3. GROSS PROFIT
        // Keep this aligned with SalesReportController:
        // SUM((selling unit price - buying price) * quantity)
        // Note: Automatically includes losses where selling_price < buying_price (negative values)
        $grossProfit = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->sum(DB::raw('(COALESCE(order_details.unitcost, 0) - COALESCE(order_details.buying_price, products.buying_price, 0)) * COALESCE(order_details.quantity, 0)'));

        $grossProfitMargin = $salesRevenue > 0 ? ($grossProfit / $salesRevenue) * 100 : 0;

        // 4. OPERATING EXPENSES
        $expenses = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Get expense breakdown by category
        $expenseBreakdown = Expense::where('shop_id', $shopId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        // 5. NET PROFIT (Gross Profit - Expenses)
        $netProfit = $grossProfit - $expenses;
        $netProfitMargin = $salesRevenue > 0 ? ($netProfit / $salesRevenue) * 100 : 0;

        // 6. CASH FLOW - INFLOW (Money received)
        $cashInflow = [
            'cash_sales' => Order::where('shop_id', $shopId)
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('payment_type', 'Cash')
                ->sum('pay'),

            'card_sales' => Order::where('shop_id', $shopId)
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('payment_type', 'Card')
                ->sum('pay'),

            'credit_received' => Order::where('shop_id', $shopId)
                ->whereBetween('order_date', [$startDate, $endDate])
                ->whereIn('payment_type', ['Due', 'Partial'])
                ->sum('pay'),
        ];

        $totalInflow = array_sum($cashInflow);

        // 7. CASH FLOW - OUTFLOW (Money paid out)
        $cashOutflow = [
            'supplier_payments' => CreditPurchasePayment::where('shop_id', $shopId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('payment_amount'),

            'expenses' => $expenses,
        ];

        $totalOutflow = array_sum($cashOutflow);

        // 8. NET CASH FLOW
        $netCashFlow = $totalInflow - $totalOutflow;

        // 9. PURCHASES & PAYABLES
        $totalPurchases = CreditPurchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_amount');

        $purchasesPaid = CreditPurchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('paid_amount');

        $purchasesDue = CreditPurchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('due_amount');

        // 10. RECEIVABLES
        $creditSales = Order::where('shop_id', $shopId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('payment_type', ['Due', 'Partial'])
            ->sum('due');

        // 11. DAILY BREAKDOWN
        $dailyBreakdown = DB::table('orders')
            ->where('shop_id', $shopId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date')
            ->get();

        // 12. TOP SELLING PRODUCTS
        $topProducts = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.total) as total_revenue'),
                DB::raw('SUM(order_details.quantity * COALESCE(order_details.buying_price, products.buying_price, 0)) as total_cost')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return [
            // Revenue & Profit
            'sales_revenue' => $salesRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $salesRevenue / $totalOrders : 0,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'gross_profit_margin' => $grossProfitMargin,

            // Expenses
            'total_expenses' => $expenses,
            'expense_breakdown' => $expenseBreakdown,

            // Net Profit
            'net_profit' => $netProfit,
            'net_profit_margin' => $netProfitMargin,

            // Cash Flow
            'cash_inflow' => $cashInflow,
            'total_inflow' => $totalInflow,
            'cash_outflow' => $cashOutflow,
            'total_outflow' => $totalOutflow,
            'net_cash_flow' => $netCashFlow,

            // Purchases
            'total_purchases' => $totalPurchases,
            'purchases_paid' => $purchasesPaid,
            'purchases_due' => $purchasesDue,

            // Receivables
            'credit_sales_outstanding' => $creditSales,

            // Detailed Data
            'daily_breakdown' => $dailyBreakdown,
            'top_products' => $topProducts,
        ];
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;

        if (!$activeShop) {
            return redirect()->route('dashboard')->with('error', 'Please select an active shop first.');
        }

        $reportData = $this->generateShopReport($activeShop->id, $selectedMonth);

        $pdf = PDF::loadView('reports.business.monthly-pdf', compact('reportData', 'selectedMonth', 'activeShop'));

        return $pdf->download('business-report-' . $selectedMonth->format('Y-m') . '.pdf');
    }

    /**
     * Compare multiple shops (for admin/owner)
     */
    public function compareShops(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01')->startOfMonth();

        // Get all shops for current user
        $shops = Shop::where('owner_id', auth()->user()->id)
            ->orWhereHas('users', function($q) {
                $q->where('users.id', auth()->id());
            })
            ->get();

        $shopsData = [];
        foreach ($shops as $shop) {
            $shopsData[$shop->id] = [
                'shop' => $shop,
                'report' => $this->generateShopReport($shop->id, $selectedMonth)
            ];
        }

        return view('reports.business.compare', compact('shopsData', 'selectedMonth'));
    }
}
