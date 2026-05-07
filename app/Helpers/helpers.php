<?php

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency
     */
    function format_currency($amount, $currency = 'LKR')
    {
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string
     */
    function format_date($date, $format = 'd/m/Y')
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }

        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('cents_to_currency')) {
    /**
     * Format currency (no conversion needed - kept for backward compatibility)
     */
    function cents_to_currency($amount)
    {
        return number_format($amount, 2);
    }
}

if (!function_exists('currency_to_cents')) {
    /**
     * Return currency as-is (no conversion needed - kept for backward compatibility)
     */
    function currency_to_cents($amount)
    {
        return $amount;
    }
}

if (!function_exists('shop_type_route_key')) {
    /**
     * Map a shop type enum value to the route/view prefix.
     */
    function shop_type_route_key(string $shopType): string
    {
        return match ($shopType) {
            'tech_shop' => 'tech',
            default => 'tech',
        };
    }
}

if (!function_exists('shop_route')) {
    /**
     * Build a shop-type-prefixed route name using the active shop.
     */
    function shop_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';

        $prefixedName = str_starts_with($name, $shopType . '.')
            ? $name
            : $shopType . '.' . $name;

        if (\Illuminate\Support\Facades\Route::has($prefixedName)) {
            return route($prefixedName, $parameters, $absolute);
        }

        if (\Illuminate\Support\Facades\Route::has($name)) {
            return route($name, $parameters, $absolute);
        }

        return url()->current();
    }
}

if (!function_exists('active_shop_type')) {
    /**
     * Resolve the active shop type key for routing/views.
     */
    function active_shop_type(): string
    {
        $fromSession = session('active_shop_type');
        if (is_string($fromSession) && $fromSession !== '') {
            return $fromSession;
        }

        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        if ($activeShop && $activeShop->shop_type) {
            return shop_type_route_key($activeShop->shop_type->value);
        }

        return 'tech';
    }
}

if (!function_exists('shop_route_exists')) {
    /**
     * Check if a shop-prefixed route (or base route) exists.
     */
    function shop_route_exists(string $name): bool
    {
        $user = auth()->user();
        $activeShop = $user?->getActiveShop();

        $shopType = $activeShop && $activeShop->shop_type
            ? shop_type_route_key($activeShop->shop_type->value)
            : 'tech';

        $prefixedName = str_starts_with($name, $shopType . '.')
            ? $name
            : $shopType . '.' . $name;

        if (\Illuminate\Support\Facades\Route::has($prefixedName)) {
            return true;
        }

        return \Illuminate\Support\Facades\Route::has($name);
    }
}

if (!function_exists('safe_count')) {
    /**
     * Safely count arrays, Countable, or Traversable values.
     */
    function safe_count($value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_array($value) || $value instanceof Countable) {
            return count($value);
        }

        if ($value instanceof Traversable) {
            return iterator_count($value);
        }

        return 0;
    }
}
