<?php

namespace App\ShopTypes\Shared\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

abstract class BaseShopTypePageController extends Controller
{
    protected const SHOP_TYPE = '';

    protected function shopView(string $view, array $data = []): View
    {
        return view('shop-types.' . static::SHOP_TYPE . '.' . $view, $data);
    }
}
