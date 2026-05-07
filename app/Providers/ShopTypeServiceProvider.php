<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
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
            'tech' => ['prefix' => 'tech', 'name' => 'tech.'],
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
        // Register all shop type namespaces
        $shopTypes = ['tech'];

        foreach ($shopTypes as $shopType) {
            View::addNamespace($shopType, resource_path('views/shop-types/' . $shopType));
        }

        // Resolve view path from the authenticated user's active shop on every request.
        View::composer('*', function (): void {
            $this->applyActiveShopTypeViewPath();
        });
    }

    /**
     * Apply active shop type as the first view path and share active shop context globally.
     */
    protected function applyActiveShopTypeViewPath(): void
    {
        $viewFinder = View::getFinder();
        if (!$viewFinder instanceof FileViewFinder) {
            return;
        }

        /** @var User|null $user */
        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';

        $activeShopViewPath = resource_path('views/shop-types/' . $shopType);
        if (!is_dir($activeShopViewPath)) {
            $activeShopViewPath = resource_path('views/shop-types/tech');
            $shopType = 'tech';
        }

        $paths = $viewFinder->getPaths();
        $shopTypesBasePath = str_replace('\\\\', '/', resource_path('views/shop-types'));

        // Remove existing shop-type paths so the active one is always first and deterministic.
        $paths = array_values(array_filter($paths, function (string $path) use ($shopTypesBasePath): bool {
            $normalizedPath = str_replace('\\\\', '/', $path);
            return !str_starts_with($normalizedPath, $shopTypesBasePath . '/');
        }));

        array_unshift($paths, $activeShopViewPath);
        $viewFinder->setPaths(array_values(array_unique($paths)));

        view()->share('activeShopType', $shopType);
        view()->share('activeShop', $activeShop);
    }
}
