<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class WarrantyClaimController extends \App\Http\Controllers\WarrantyClaimController
{
	public function index(Request $request)
	{
		$response = parent::index($request);
		return view('shop-types.tech.warranty.index', $response->getData());
	}

	public function create()
	{
		$response = parent::create();
		return view('shop-types.tech.warranty.create', $response->getData());
	}

	public function show(WarrantyClaim $warrantyClaim)
	{
		$response = parent::show($warrantyClaim);
		return view('shop-types.tech.warranty.show', $response->getData());
	}

	public function edit(WarrantyClaim $warrantyClaim)
	{
		$response = parent::edit($warrantyClaim);
		return view('shop-types.tech.warranty.edit', $response->getData());
	}
}
