<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckShopFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $feature
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $shop = $user->shop_id ? \App\Models\Shop::find($user->shop_id) : null;

        if (!$shop) {
            abort(403, 'No shop assigned');
        }

        if (!$shop->hasFeature($feature)) {
            abort(403, "This feature ({$feature}) is not available for your shop type.");
        }

        return $next($request);
    }
}
