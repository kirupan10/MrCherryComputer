<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('sales')
            ->withSum('sales', 'total_amount');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Request $request, Customer $customer)
    {
        $filters = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'payment_status' => 'nullable|in:paid,partial,unpaid',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        $salesQuery = $customer->sales()
            ->with('items.product')
            ->latest('sale_date');

        if (!empty($filters['from_date'])) {
            $salesQuery->whereDate('sale_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $salesQuery->whereDate('sale_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['payment_status'])) {
            $salesQuery->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['status'])) {
            $salesQuery->where('status', $filters['status']);
        }

        $sales = $salesQuery->paginate(10)->withQueryString();

        $filteredSummaryQuery = clone $salesQuery;

        $stats = [
            'total_sales' => $customer->sales()->sum('total_amount'),
            'total_orders' => $customer->sales()->count(),
            'last_purchase' => $customer->sales()->latest('sale_date')->first()?->sale_date,
            'average_order_value' => $customer->sales()->avg('total_amount') ?? 0,
        ];

        $filteredStats = [
            'filtered_sales' => $filteredSummaryQuery->sum('total_amount'),
            'filtered_orders' => $filteredSummaryQuery->count(),
        ];

        return view('customers.show', compact('customer', 'stats', 'sales', 'filters', 'filteredStats'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // Check if customer has sales
        if ($customer->sales()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete customer with existing sales records.']);
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $customers = Customer::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'company_name']);

        return response()->json($customers);
    }
}
