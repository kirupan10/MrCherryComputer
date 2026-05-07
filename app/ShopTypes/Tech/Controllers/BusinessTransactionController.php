<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\BusinessTransaction;
use Illuminate\Http\Request;

class BusinessTransactionController extends \App\Http\Controllers\BusinessTransactionController
{
	public function index(Request $request)
	{
		$response = parent::index($request);
		return view('shop-types.tech.business-transactions.index', $response->getData());
	}

	public function create()
	{
		$response = parent::create();
		return view('shop-types.tech.business-transactions.create', $response->getData());
	}

	public function show(BusinessTransaction $transaction)
	{
		$response = parent::show($transaction);
		return view('shop-types.tech.business-transactions.show', $response->getData());
	}

	public function edit(BusinessTransaction $transaction)
	{
		$response = parent::edit($transaction);
		return view('shop-types.tech.business-transactions.edit', $response->getData());
	}
}
