<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class WarrantyClaimController extends \App\Http\Controllers\WarrantyClaimController
{
	protected function indexRoute(): string
	{
		return 'tech.warranty-claims.index';
	}

	public function index(Request $request)
	{
		$response = parent::index($request);
		return view('warranty.index', $response->getData());
	}

	public function create()
	{
		$response = parent::create();
		return view('warranty.create', $response->getData());
	}

	public function show(WarrantyClaim $warrantyClaim)
	{
		$response = parent::show($warrantyClaim);
		return view('warranty.show', $response->getData());
	}

	public function edit(WarrantyClaim $warrantyClaim)
	{
		$response = parent::edit($warrantyClaim);
		return view('warranty.edit', $response->getData());
	}
}
