<?php

namespace App\Http\Requests\Auth;

use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    //protected $inputType;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $this->email => trans('auth.failed'),
            ]);
        }

        // Check if user is suspended
        $user = Auth::user();
        if ($user && $user->is_suspended) {
            // Auto-unsuspend if suspension has expired
            if ($user->suspension_ends_at && now()->greaterThan($user->suspension_ends_at)) {
                $user->update([
                    'is_suspended' => false,
                    'suspension_reason' => null,
                    'suspension_type' => null,
                    'suspension_duration' => null,
                    'suspended_at' => null,
                    'suspension_ends_at' => null,
                    'suspended_by' => null,
                    'last_login_at' => now(),
                ]);
            } else {
                AuditLog::create([
                    'shop_id' => null,
                    'user_id' => $user->id,
                    'action' => 'login_blocked_user_suspended',
                    'model_type' => 'User',
                    'model_id' => $user->id,
                    'description' => "Suspended user '{$user->name}' attempted to log in",
                    'old_data' => null,
                    'new_data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'user_role' => $user->role,
                        'suspension_reason' => $user->suspension_reason,
                        'suspension_type' => $user->suspension_type,
                        'suspension_ends_at' => $user->suspension_ends_at ? $user->suspension_ends_at->toDateTimeString() : null,
                        'shop_id' => $user->shop_id,
                    ],
                    'ip_address' => $this->ip(),
                    'user_agent' => $this->userAgent(),
                ]);

                // User is still suspended - logout and show error
                Auth::logout();

                $suspensionDetails = '';

                if ($user->suspension_ends_at) {
                    $suspensionDetails = 'This suspension will be lifted on ' . $user->suspension_ends_at->format('F d, Y \a\t H:i A') . '.';
                } elseif ($user->suspension_type === 'lifetime') {
                    $suspensionDetails = 'This is a permanent suspension.';
                } elseif ($user->suspension_type === 'until_payment') {
                    $suspensionDetails = 'Please complete your payment to reactivate your account.';
                } elseif ($user->suspension_type === 'manual') {
                    $suspensionDetails = 'Your account is under review by our team.';
                }

                $suspensionDetails .= ' Contact administrator at 077 022 1046 to lift the suspension.';

                throw ValidationException::withMessages([
                    'email' => 'Your account has been suspended.|' . $user->suspension_reason . '|' . $suspensionDetails,
                ]);
            }
        } else {
            $shop = $user ? $user->shop : null;
            if ($user && $shop && $shop->is_suspended) {
                AuditLog::create([
                    'shop_id' => null,
                    'user_id' => $user->id,
                    'action' => 'login_blocked_shop_suspended',
                    'model_type' => 'Shop',
                    'model_id' => $shop->id,
                    'description' => "User '{$user->name}' attempted to log in while shop '{$shop->name}' is suspended",
                    'old_data' => null,
                    'new_data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'user_role' => $user->role,
                        'shop_id' => $shop->id,
                        'shop_name' => $shop->name,
                        'suspension_reason' => $shop->suspension_reason,
                        'suspended_at' => $shop->suspended_at ? $shop->suspended_at->toDateTimeString() : null,
                    ],
                    'ip_address' => $this->ip(),
                    'user_agent' => $this->userAgent(),
                ]);

                Auth::logout();

                $shopDetails = $shop->suspension_reason ? $shop->suspension_reason : 'Your shop is suspended.';
                throw ValidationException::withMessages([
                    'email' => 'Your shop has been suspended.|' . $shopDetails,
                ]);
            }

            // Update last login for non-suspended users
            if ($user) {
                $user->update(['last_login_at' => now()]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}
