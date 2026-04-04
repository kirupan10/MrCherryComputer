<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function userHasRole($user, array|string $roles): bool
    {
        if (!$user || !method_exists($user, 'hasRole')) {
            return false;
        }

        return (bool) call_user_func([$user, 'hasRole'], $roles);
    }

    private function buildQuickActions($user): array
    {
        $actions = [
            [
                'label' => 'New Sale',
                'route' => route('pos.index'),
                'iconClass' => 'text-blue-600',
                'iconPath' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            [
                'label' => 'View Sales',
                'route' => route('sales.index'),
                'iconClass' => 'text-yellow-600',
                'iconPath' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
        ];

        if ($this->userHasRole($user, ['admin', 'manager'])) {
            $actions[] = [
                'label' => 'Add Product',
                'route' => route('products.create'),
                'iconClass' => 'text-green-600',
                'iconPath' => 'M12 4v16m8-8H4',
            ];

            $actions[] = [
                'label' => 'Add Customer',
                'route' => route('customers.create'),
                'iconClass' => 'text-purple-600',
                'iconPath' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ];

            $actions[] = [
                'label' => 'Reports',
                'route' => route('reports.index'),
                'iconClass' => 'text-red-600',
                'iconPath' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            ];

            $actions[] = [
                'label' => 'Products',
                'route' => route('products.index'),
                'iconClass' => 'text-indigo-600',
                'iconPath' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ];
        }

        return $actions;
    }

    public function index()
    {
        $user = Auth::user();

        // Common data for all roles
        $data = [
            'userRole' => $user->roles->first()->name ?? 'No Role',
        ];

        if ($this->userHasRole($user, ['admin', 'manager'])) {
            // Admin and Manager Dashboard
            $data['todaySales'] = Sale::today()->completed()->sum('total_amount');
            $data['todaySalesCount'] = Sale::today()->completed()->count();
            $data['monthSales'] = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->completed()
                ->sum('total_amount');
            $data['totalSales'] = $data['monthSales']; // Use monthly sales for total
            $data['totalCustomers'] = Customer::count();
            $data['lowStockProducts'] = Product::with(['category', 'stock'])
                ->lowStock()
                ->limit(10)
                ->get();
            $data['lowStockCount'] = Product::lowStock()->count();
            $data['recentSales'] = Sale::with(['customer', 'creator'])
                ->latest()
                ->limit(10)
                ->get();
            $data['pendingExpenses'] = Expense::where('status', 'pending')->count();

            if ($this->userHasRole($user, 'admin')) {
                $data['todayExpenses'] = Expense::today()->sum('amount');
                $data['monthExpenses'] = Expense::thisMonth()->sum('amount');
            }
        } else {
            // Cashier Dashboard
            $data['mySalesToday'] = Sale::today()
                ->where('created_by', $user->id)
                ->completed()
                ->sum('total_amount');
            $data['mySalesCount'] = Sale::today()
                ->where('created_by', $user->id)
                ->completed()
                ->count();
            $data['myRecentSales'] = Sale::with(['customer'])
                ->where('created_by', $user->id)
                ->latest()
                ->limit(10)
                ->get();

            // Stats visible to all roles
            $data['totalSales'] = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->completed()
                ->sum('total_amount');
            $data['todaySales'] = Sale::today()->completed()->sum('total_amount');
            $data['lowStockCount'] = Product::lowStock()->count();
        }

        $data['quickActions'] = $this->buildQuickActions($user);

        return view('dashboard', $data);
    }
}
