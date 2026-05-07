<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\CreditPurchase;
use Illuminate\Http\Request;

class PurchaseController extends \App\Http\Controllers\PurchaseController
{
	public function index(Request $request)
	{
		$response = parent::index($request);
		return view('shop-types.tech.credit-purchases.index', $response->getData());
	}

	public function create()
	{
		$response = parent::create();
		return view('shop-types.tech.credit-purchases.create', $response->getData());
	}

	public function show(CreditPurchase $creditPurchase)
	{
		$response = parent::show($creditPurchase);
		return view('shop-types.tech.credit-purchases.show', $response->getData());
	}

	public function edit(CreditPurchase $creditPurchase)
	{
		$response = parent::edit($creditPurchase);
		return view('shop-types.tech.credit-purchases.edit', $response->getData());
	}
}
