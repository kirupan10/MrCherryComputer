<?php

namespace App\ShopTypes\Tech\Controllers;

class DeliveryController extends \App\Http\Controllers\DeliveryController
{
    protected function indexRoute(): string
    {
        return 'tech.deliveries.index';
    }
}
