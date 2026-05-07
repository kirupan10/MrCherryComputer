<?php

namespace App\ShopTypes\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;

class BaseCustomerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopId = $activeShop ? $activeShop->id : null;

        $customers = Customer::withCount('orders')
            ->latest()
            ->paginate(20);

        $ordersQuery = \App\Models\Order::query();
        if ($shopId) {
            $ordersQuery->where('shop_id', $shopId);
        }
        $totalPurchasesCents = $ordersQuery->sum('total');

        $customersQuery = Customer::query();
        $newToday = $customersQuery->whereDate('created_at', '>=', today())
            ->count();
        $newThisMonth = Customer::whereDate('created_at', '>=', now()->startOfMonth())
            ->count();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = "shop-types.{$shopType}.customers.index";

        if (!view()->exists($viewName)) {
            $viewName = 'shop-types.tech.customers.index';
        }

        return view($viewName, [
            'customers' => $customers,
            'total_purchases_cents' => $totalPurchasesCents,
            'new_today_count' => $newToday,
            'new_this_month_count' => $newThisMonth,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = "shop-types.{$shopType}.customers.create";

        if (!view()->exists($viewName)) {
            $viewName = 'shop-types.tech.customers.create';
        }

        return view($viewName);
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $data = $request->all();
            if (!empty($data['phone'])) {
                $data['phone'] = preg_replace('/\s+/', '', $data['phone']);
            }

            $customer = Customer::create($data);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                $file->storeAs('customers/', $filename, 'public');
                $customer->update([
                    'photo' => $filename,
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'New customer has been created!',
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'date_of_birth' => $customer->date_of_birth,
                        'address' => $customer->address,
                    ],
                ]);
            }

            return redirect()
                ->to(shop_route('customers.index'))
                ->with('success', 'New customer has been created!');
        } catch (\Exception $e) {
            \Log::error('Customer creation failed: ' . $e->getMessage(), [
                'data' => $request->except(['photo']),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        $customer->loadMissing(['orders.details']);

        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = "shop-types.{$shopType}.customers.show";

        if (!view()->exists($viewName)) {
            $viewName = 'shop-types.tech.customers.show';
        }

        return view($viewName, [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer)
    {
        if ($customer->isWalkInCustomer()) {
            return redirect()
                ->to(shop_route('customers.index'))
                ->with('error', 'Walk-In Customer cannot be edited.');
        }

        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;
        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';
        $viewName = "shop-types.{$shopType}.customers.edit";

        if (!view()->exists($viewName)) {
            $viewName = 'shop-types.tech.customers.edit';
        }

        return view($viewName, [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        if ($customer->isWalkInCustomer()) {
            return redirect()
                ->to(shop_route('customers.index'))
                ->with('error', 'Walk-In Customer cannot be modified.');
        }

        $data = $request->except('photo');
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/\s+/', '', $data['phone']);
        }
        $customer->update($data);

        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                unlink(public_path('storage/customers/') . $customer->photo);
            }

            $file = $request->file('photo');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('customers/', $fileName, 'public');

            $customer->update([
                'photo' => $fileName,
            ]);
        }

        return redirect()
            ->to(shop_route('customers.index'))
            ->with('success', 'Customer has been updated!');
    }

    public function updateAjax(UpdateCustomerRequest $request, Customer $customer)
    {
        if ($customer->isWalkInCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'Walk-In Customer cannot be modified.',
            ], 403);
        }

        try {
            $customer->update($request->except('photo'));

            return response()->json([
                'success' => true,
                'message' => 'Customer details updated successfully!',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'account_holder' => $customer->account_holder,
                    'account_number' => $customer->account_number,
                    'bank_name' => $customer->bank_name,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer details',
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        if ($customer->isWalkInCustomer()) {
            return redirect()
                ->back()
                ->with('error', 'Walk-In Customer cannot be deleted.');
        }

        if ($customer->photo) {
            unlink(public_path('storage/customers/') . $customer->photo);
        }

        $customer->delete();

        return redirect()
            ->back()
            ->with('success', 'Customer has been deleted!');
    }
}
