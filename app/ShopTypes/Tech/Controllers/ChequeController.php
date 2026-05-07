<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\Cheque;
use Illuminate\Http\Request;

class ChequeController extends \App\Http\Controllers\ChequeController
{
	public function index(Request $request)
	{
		$response = parent::index($request);
		return view('shop-types.tech.cheques.index', $response->getData());
	}

	public function create()
	{
		$response = parent::create();
		return view('shop-types.tech.cheques.create', $response->getData());
	}

	public function show(Cheque $cheque)
	{
		$response = parent::show($cheque);
		return view('shop-types.tech.cheques.show', $response->getData());
	}

	public function edit(Cheque $cheque)
	{
		$response = parent::edit($cheque);
		return view('shop-types.tech.cheques.edit', $response->getData());
	}
}
