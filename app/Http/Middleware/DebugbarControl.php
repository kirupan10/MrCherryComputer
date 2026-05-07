<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugbarControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disable debugbar for all users except specific admin email
        // This prevents N+1 query alerts and other debug information from showing to regular users
        if (class_exists(\Barryvdh\Debugbar\Facade::class)) {
            $user = auth()->user();
            
            // Only enable debugbar for super admin or specific development email
            $allowedEmails = [
                'ikirupan@gmail.com', // Development account
            ];
            
            if (!$user || !$user->isAdmin() || !in_array($user->email, $allowedEmails)) {
                \Debugbar::disable();
            }
        }

        return $next($request);
    }
}
