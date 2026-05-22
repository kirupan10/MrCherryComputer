<?php

use App\Models\User;
use App\Models\Shop;

if (! function_exists('safe_count')) {
    /**
     * Return a safe count for collections/paginators/arrays.
     * - If object has total() (LengthAwarePaginator), return that.
     * - If it's countable, return count().
     * - Otherwise return 0.
     *
     * @param mixed $value
     * @return int
     */
    function safe_count($value): int
    {
        if (is_null($value)) {
            return 0;
        }

        // If paginator or object exposes total()
        if (is_object($value) && method_exists($value, 'total')) {
            try {
                $t = $value->total();
                return is_numeric($t) ? (int) $t : 0;
            } catch (\Throwable $e) {
                // fallthrough
            }
        }

        if (is_countable($value)) {
            return count($value);
        }

        return 0;
    }
}

if (! function_exists('shop_type_route_key')) {
    /**
     * Normalize shop type enum values to route namespace keys.
     */
    function shop_type_route_key(string $shopType): string
    {
        return match ($shopType) {
            'tech_shop' => 'tech',
            default => $shopType,
        };
    }
}

if (! function_exists('shop_route')) {
    /**
     * Generate a shop-type aware route URL.
     * If user has an active shop with a specific shop type, prepend the shop type to the route name.
     *
    * Example: shop_route('profile') becomes route('tech.profile') for tech shop users
     *
     * @param string $name The route name without shop type prefix
     * @param mixed $parameters Route parameters
     * @param bool $absolute Whether to generate absolute URL
     * @return string
     */
    function shop_route(string $name, $parameters = [], bool $absolute = true): string
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            // Try base route if no user
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name, $parameters, $absolute);
            }
            // Fallback to dashboard
            return route('dashboard');
        }

        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            // Try base route if no shop/shop_type
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name, $parameters, $absolute);
            }
            // Fallback to dashboard
            return route('dashboard');
        }

        $shopType = shop_type_route_key($shop->shop_type->value);
        if ($shopType === 'tech' && str_starts_with($name, 'reports.sales.')) {
            $name = 'sales.' . substr($name, strlen('reports.sales.'));
        }
        $shopTypeRouteName = "{$shopType}.{$name}";

        // Check if shop-type specific route exists
        if (\Illuminate\Support\Facades\Route::has($shopTypeRouteName)) {
            return route($shopTypeRouteName, $parameters, $absolute);
        }

        // Try base route as fallback
        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, $parameters, $absolute);
        }

        // Last resort: return to shop dashboard
        $shopDashboardRoute = "{$shopType}.dashboard";
        if (\Illuminate\Support\Facades\Route::has($shopDashboardRoute)) {
            return route($shopDashboardRoute);
        }

        // Ultimate fallback
        return route('dashboard');
    }
}

if (! function_exists('shop_route_exists')) {
    function shop_route_exists(string $name): bool
    {
        $user = auth()->user();
        if (!$user instanceof User) return false;

        $shop = $user->getActiveShop();
        if (!$shop || !$shop->shop_type) return false;

        $shopType = shop_type_route_key($shop->shop_type->value);
        return \Illuminate\Support\Facades\Route::has("{$shopType}.{$name}");
    }
}

if (! function_exists('active_shop_type')) {
    /**
     * Get the active shop for the current user.
     */
    function active_shop(): ?Shop
    {
        $user = auth()->user();

        if (!$user instanceof User) {
            return null;
        }

        return $user->getActiveShop();
    }
}

if (! function_exists('active_shop_type')) {
    /**
     * Get the active shop type for the current user
     *
     * @return string|null
     */
    function active_shop_type(): ?string
    {
        $shop = active_shop();
        if (!$shop || !$shop->shop_type) {
            return null;
        }

        return shop_type_route_key($shop->shop_type->value);
    }
}

