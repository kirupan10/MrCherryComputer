<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for non-authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            return $next($request);
        }

        // Skip for non-shop-owners or logout
        if (!$user->isShopOwner() ||
            $request->routeIs('logout')) {
            return $next($request);
        }

        // Single-shop mode relies on the persisted current shop only.
        if (!$user->getActiveShop()) {
            return redirect()->route('profile.edit')
                ->with('error', 'No active shop is assigned to this account.');
        }

        return $next($request);
    }
}
