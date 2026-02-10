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
    public function index()
    {
        $user = Auth::user();

        // Common data for all roles
        $data = [
            'userRole' => $user->roles->first()->name ?? 'No Role',
        ];

        if ($user->hasRole(['admin', 'manager'])) {
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

            if ($user->hasRole('admin')) {
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

        return view('dashboard', $data);
    }
}
