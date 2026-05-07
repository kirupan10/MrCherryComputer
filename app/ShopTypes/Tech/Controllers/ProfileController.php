<?php

namespace App\ShopTypes\Tech\Controllers;

use Illuminate\Http\Request;

class ProfileController extends \App\Http\Controllers\ProfileController
{
	public function features(Request $request)
	{
		$user = $request->user();
		$shop = $user?->getActiveShop();

		if (!$shop || !$shop->shop_type) {
			return redirect()->to(shop_route('profile.settings'));
		}

		return view('shop-types.tech.profile.features', [
			'user' => $user,
			'shop' => $shop,
			'shopSettings' => $shop->shop_settings ?? [],
		]);
	}
}
