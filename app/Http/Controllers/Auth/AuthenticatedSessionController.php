<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (view()->exists('auth.login')) {
            return view('auth.login');
        }

        if (view()->exists('shop-types.tech.auth.login')) {
            return view('shop-types.tech.auth.login');
        }

        abort(500, 'Login view is not configured.');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->intended(route('dashboard'));
        }

        // Log the successful login
        AuditLog::create([
            'shop_id' => $user->shop_id,
            'user_id' => $user->id,
            'action' => 'user_login',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => "User '{$user->name}' logged in successfully",
            'old_data' => null,
            'new_data' => [
                'login_time' => now()->toDateTimeString(),
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Route shop users to their shop-type dashboard URL (e.g. /tech).
        if (!$user->isAdmin()) {
            return redirect()->to(shop_route('dashboard'));
        }

        // Admin users still use the generic dashboard flow.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Log the logout before destroying the session
        if ($user) {
            AuditLog::create([
                'shop_id' => $user->shop_id,
                'user_id' => $user->id,
                'action' => 'user_logout',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User '{$user->name}' logged out",
                'old_data' => null,
                'new_data' => [
                    'logout_time' => now()->toDateTimeString(),
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
