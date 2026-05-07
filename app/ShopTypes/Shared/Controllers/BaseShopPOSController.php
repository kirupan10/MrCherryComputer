<?php

namespace App\ShopTypes\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Order\OrderController;
class BaseShopPOSController extends Controller
{
    public function index()
    {
        // Reuse current POS order create flow while keeping per-shop controller structure.
        return app(OrderController::class)->create();
    }
}
