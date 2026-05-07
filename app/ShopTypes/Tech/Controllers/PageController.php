<?php

namespace App\ShopTypes\Tech\Controllers;

use App\ShopTypes\Shared\Controllers\BaseShopTypePageController;
use Illuminate\Contracts\View\View;

class PageController extends BaseShopTypePageController
{
    protected const SHOP_TYPE = 'tech';

    public function dashboard(): View
    {
        $activeShop = auth()->user()?->getActiveShop();

        return $this->shopView('dashboard', [
            'shopName' => $activeShop?->name ?? 'Tech Shop',
            'products' => 0,
            'categories' => 0,
            'shopUsers' => 0,
            'orders' => 0,
            'creditSalesCount' => 0,
            'totalSales' => 0,
            'profitOrLoss' => 0,
            'turnover' => 0,
            'upcomingCheques' => collect(),
            'creditOutstanding' => 0,
            'upcomingPayments' => collect(),
        ]);
    }

    public function reportsWarranty(): View
    {
        return $this->shopView('reports.warranty');
    }

    public function reportsIndex(): View
    {
        return $this->shopView('reports.index');
    }

    public function reportsRepairs(): View
    {
        return $this->shopView('reports.repairs');
    }

    public function reportsSerialNumbers(): View
    {
        return $this->shopView('reports.serial-numbers');
    }
}
