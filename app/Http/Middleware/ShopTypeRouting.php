<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ShopTypeRouting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        $shopType = shop_type_route_key($shop->shop_type->value);
        $shopPrefix = $shopType;

        $requestedPrefix = $request->segment(1);
        $shopPrefixes = [
            'tech',
        ];

        // Prevent users from manually opening routes from a different shop type.
        if ($requestedPrefix && in_array($requestedPrefix, $shopPrefixes, true) && $requestedPrefix !== $shopPrefix) {
            if ($request->expectsJson()) {
                abort(403, 'You are not allowed to access this shop type.');
            }

            $dashboardRoute = "{$shopType}.dashboard";

            if (Route::has($dashboardRoute)) {
                return redirect()
                    ->route($dashboardRoute)
                    ->with('error', 'You are not allowed to access that shop type.');
            }

            abort(403, 'You are not allowed to access this shop type.');
        }

        // Store shop type in session for easy access
        session(['active_shop_type' => $shopType]);
        session(['active_shop_id' => $shop->id]);

        // Share with all views
        view()->share('activeShopType', $shopType);
        view()->share('activeShop', $shop);

        return $next($request);
    }
}
