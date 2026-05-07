<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\ShopTypes\Shared\Controllers\BaseCustomerController;

class CustomerController extends Controller
{
    private function shopController(): BaseCustomerController
    {
        return app(\App\ShopTypes\Tech\Controllers\CustomerController::class);
    }

    public function index()
    {
        return $this->shopController()->index();
    }

    public function create()
    {
        return $this->shopController()->create();
    }

    public function store(StoreCustomerRequest $request)
    {
        return $this->shopController()->store($request);
    }

    public function show(Customer $customer)
    {
        return $this->shopController()->show($customer);
    }

    public function edit(Customer $customer)
    {
        return $this->shopController()->edit($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        return $this->shopController()->update($request, $customer);
    }

    public function updateAjax(UpdateCustomerRequest $request, Customer $customer)
    {
        return $this->shopController()->updateAjax($request, $customer);
    }

    public function destroy(Customer $customer)
    {
        return $this->shopController()->destroy($customer);
    }
}
