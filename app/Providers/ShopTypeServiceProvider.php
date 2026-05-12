<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Models\User;

class ShopTypeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register shop type routes
        $this->registerShopTypeRoutes();

        // Register shop type views
        $this->registerShopTypeViews();
    }

    /**
     * Register routes for each shop type
     */
    protected function registerShopTypeRoutes(): void
    {
        // Define shop types with their route configurations
        $shopTypeRoutes = [
            'tech' => ['prefix' => '', 'name' => 'tech.'],
        ];

        foreach ($shopTypeRoutes as $type => $config) {
            $routePath = base_path("routes/shop-types/{$type}.php");
            if (file_exists($routePath)) {
                Route::middleware(['web', 'auth', \App\Http\Middleware\ShopTypeRouting::class])
                    ->prefix($config['prefix'])
                    ->name($config['name'])
                    ->group($routePath);
            }
        }
    }

    /**
     * Register view namespaces for shop types
     */
    protected function registerShopTypeViews(): void
    {
        // Share active shop context on every request.
        View::composer('*', function (): void {
            $this->applyActiveShopTypeViewPath();
        });
    }

    /**
     * Apply active shop type as the first view path and share active shop context globally.
     */
    protected function applyActiveShopTypeViewPath(): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';

        view()->share('activeShopType', $shopType);
        view()->share('activeShop', $activeShop);
    }
}
