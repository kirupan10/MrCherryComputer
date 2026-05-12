<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnforceShopTypePrefix
{
    /**
     * Resolve shop route key to URL prefix segment.
     */
    private function shopPrefixSegment(string $shopType): string
    {
        return match ($shopType) {
            'tech' => '',
            default => $shopType,
        };
    }

    /**
     * Check whether a GET path can be matched by the router.
     */
    private function canResolveGetPath(string $path): bool
    {
        try {
            $testRequest = Request::create('/' . ltrim($path, '/'), 'GET');
            app('router')->getRoutes()->match($testRequest);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Redirect generic named routes to shop-type-prefixed routes when available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('GET') || $request->expectsJson()) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user instanceof User || $user->isAdmin()) {
            return $next($request);
        }

        $shop = $user->getActiveShop();
        if (!$shop || !$shop->shop_type) {
            return $next($request);
        }

        $currentRoute = $request->route();
        $currentRouteName = $currentRoute?->getName();

        $shopType = shop_type_route_key($shop->shop_type->value);
        $shopPrefix = $this->shopPrefixSegment($shopType);

        // When shop routes are configured at root level, no prefix redirects are needed.
        if ($shopPrefix === '') {
            return $next($request);
        }

        if (!$currentRouteName) {
            $fallbackRedirect = $this->fallbackPathRedirect($request, $shopPrefix);
            return $fallbackRedirect ?: $next($request);
        }

        // Already on a shop-prefixed URL path.
        if ($request->is($shopPrefix) || $request->is($shopPrefix . '/*')) {
            return $next($request);
        }

        // Already on the shop-specific route.
        if (str_starts_with($currentRouteName, $shopType . '.')) {
            return $next($request);
        }

        $shopSpecificRouteName = $shopType . '.' . $currentRouteName;
        if (!Route::has($shopSpecificRouteName)) {
            $fallbackRedirect = $this->fallbackPathRedirect($request, $shopPrefix);
            return $fallbackRedirect ?: $next($request);
        }

        $targetUrl = route($shopSpecificRouteName, $currentRoute->parameters());

        if ($request->query()) {
            $targetUrl .= (str_contains($targetUrl, '?') ? '&' : '?') . http_build_query($request->query());
        }

        if ($targetUrl === $request->fullUrl()) {
            return $next($request);
        }

        return redirect()->to($targetUrl);
    }

    /**
     * Fallback redirect for URLs that have a shop-prefixed path equivalent.
     */
    private function fallbackPathRedirect(Request $request, string $shopPrefix): ?Response
    {
        $currentPath = trim($request->path(), '/');
        if ($currentPath === '') {
            return null;
        }

        $excludedStarts = [
            'admin',
            'api',
            'broadcasting',
            'horizon',
            'sanctum',
            'storage',
            'vendor',
            'livewire',
            'login',
            'logout',
            'register',
            'password',
            'shop',
        ];

        foreach ($excludedStarts as $excluded) {
            if ($currentPath === $excluded || str_starts_with($currentPath, $excluded . '/')) {
                return null;
            }
        }

        $targetPath = $shopPrefix . '/' . $currentPath;
        if (!$this->canResolveGetPath($targetPath)) {
            return null;
        }

        $targetUrl = url('/' . $targetPath);
        if ($request->query()) {
            $targetUrl .= '?' . http_build_query($request->query());
        }

        if ($targetUrl === $request->fullUrl()) {
            return null;
        }

        return redirect()->to($targetUrl);
    }
}
