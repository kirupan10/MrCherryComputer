<?php

namespace App\Http\Controllers\Dashboards;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\KpiService;


class DashboardController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (!$user instanceof User) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            // Admin sees system-wide statistics
            return $this->adminDashboard();
        } elseif ($user->isShopOwner() || $user->isManager() || $user->isEmployee()) {
            // Shop-specific users see their shop's statistics
            return $this->shopDashboard($user);
        }

        // Fallback for other roles
        return $this->basicDashboard();
    }

    private function adminDashboard()
    {
        // Get shop statistics
        $totalShops = Shop::count();
        $activeShops = Shop::where('subscription_status', 'active')->where('is_active', true)->count();
        $suspendedShops = Shop::where('subscription_status', 'suspended')->count();
        $overdueShops = Shop::where('subscription_end_date', '<', now())
            ->where('subscription_status', '!=', 'suspended')
            ->count();

        $stats = [
            'total_shops' => $totalShops,
            'active_shops' => $activeShops,
            'suspended_shops' => $suspendedShops,
            'overdue_shops' => $overdueShops,
        ];

        // Add global order KPIs from DB-side cache/view to avoid heavy aggregates here
        $kpiService = new KpiService();
        $orderKpis = $kpiService->getOrderKpis();
        $stats['total_orders'] = $orderKpis->total_orders ?? 0;
        $stats['orders_total_amount_cents'] = $orderKpis->total_amount ?? 0;
        $stats['completed_orders'] = $orderKpis->completed_count ?? 0;

        // Get overdue shops for the alert section
        $overdueShops = Shop::where('subscription_end_date', '<', now())
            ->where('subscription_status', '!=', 'suspended')
            ->with('owner')
            ->orderBy('subscription_end_date', 'asc')
            ->get();

        return view('admin.dashboard', compact('stats', 'overdueShops'));
    }

    private function shopDashboard(User $user)
    {
        $activeShop = $user->getActiveShop();

        // Admin can work without a shop, redirect to admin dashboard instead
        if (!$activeShop && $user->isAdmin()) {
            return $this->adminDashboard();
        }

        if (!$activeShop) {
            return redirect()->route('profile.edit')->with('error', 'No active shop assigned. Please contact administrator.');
        }

        // Use KpiService per-shop stored-proc for shop-level order KPIs (fast)
        $kpiService = new KpiService();
        $shopKpis = $kpiService->getOrderKpisByShop($activeShop->id);

        $orders = $shopKpis->total_orders ?? 0;
        $completedOrders = $shopKpis->completed_count ?? 0;

        // Keep counts for products/categories (these are simple count queries)
        $products = Product::where('shop_id', $activeShop->id)->count();
        $categories = Category::where('shop_id', $activeShop->id)
            ->orWhereNull('shop_id') // Include universal categories
            ->count();

        // Get shop users count (users belonging to this shop)
        $shopUsers = User::where('shop_id', $activeShop->id)->count();

        // Low stock products - query products table directly
        $lowStockProducts = \Illuminate\Support\Facades\DB::table('products')
            ->whereRaw('quantity <= quantity_alert')
            ->where('shop_id', $activeShop->id)
            ->count();

        // Calculate total sales (turnover)
        $totalSales = $shopKpis->total_amount ?? 0; // cents

        // Expenses (sum of all expenses for this shop)
        $expenseKpis = (new KpiService())->getExpenseKpisByShop($activeShop->id);
        $totalExpenses = $expenseKpis->total_expenses ?? 0;

        // Profit/Loss
        $profitOrLoss = $totalSales - $totalExpenses;

        // Upcoming cheques (next 5 by date, pending only)
        $upcomingCheques = \App\Models\Cheque::byShop($activeShop->id)
            ->pending()
            ->where('cheque_date', '>=', now())
            ->orderBy('cheque_date', 'asc')
            ->take(5)
            ->get();

        // Credit payment outstanding (sum of due_amount for pending/partial credit sales)
        $creditOutstanding = \App\Models\CreditSale::where('shop_id', $activeShop->id)
            ->whereIn('status', ['pending', 'partial'])
            ->sum('due_amount');

        // Upcoming payments (next 5 due credit purchases or expenses)
        $upcomingPayments = \App\Models\CreditPurchase::where('shop_id', $activeShop->id)
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Get count of credit sales orders
        $creditSalesCount = \Illuminate\Support\Facades\DB::table('credit_sales')
            ->join('orders', 'credit_sales.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $activeShop->id)
            ->count();

        // Recent orders for this shop
        $recentOrders = Order::where('shop_id', $activeShop->id)
            ->with(['customer'])
            ->latest()
            ->take(5)
            ->get();

        $shopType = $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = 'dashboard';

        return view($viewName, [
            'userType' => 'shop_user',
            'shopName' => $activeShop->name,
            'shopType' => $shopType,
            'products' => $products,
            'orders' => $orders,
            'completedOrders' => $completedOrders,
            'creditSalesCount' => $creditSalesCount,
            'categories' => $categories,
            'shopUsers' => $shopUsers,
            'lowStockProducts' => $lowStockProducts,
            'totalSales' => $totalSales,
            'totalAllOrders' => $totalSales,
            'recentOrders' => $recentOrders,
            'profitOrLoss' => $profitOrLoss,
            'turnover' => $totalSales,
            'upcomingCheques' => $upcomingCheques,
            'creditOutstanding' => $creditOutstanding,
            'upcomingPayments' => $upcomingPayments,
        ]);
    }

    private function basicDashboard()
    {
        /** @var User|null $user */
        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = 'dashboard';

        return view($viewName, [
            'userType' => 'basic',
            'shopType' => $shopType,
            'products' => 0,
            'orders' => 0,
            'completedOrders' => 0,
            'categories' => 0,
        ]);
    }
}
