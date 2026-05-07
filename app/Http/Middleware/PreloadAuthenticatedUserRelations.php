<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class PreloadAuthenticatedUserRelations
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user instanceof User) {
            $user->loadMissing(['shop', 'ownedShop', 'ownedShops']);
        }

        return $next($request);
    }
}
