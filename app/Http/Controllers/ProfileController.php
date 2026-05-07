<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show user profile page (for regular users)
     */
    public function userProfile(Request $request)
    {
        $user = $request->user();
        $shop = $user->getActiveShop();

        // If user has a shop with a specific shop type, use shop-type specific view
        if ($shop && $shop->shop_type) {
            $shopType = shop_type_route_key($shop->shop_type->value);
            $shopTypeView = "shop-types.{$shopType}.profile.user-profile";

            if (view()->exists($shopTypeView)) {
                return view($shopTypeView, [
                    'user' => $user,
                    'shop' => $shop,
                ]);
            }
        }

        // Fallback to default profile view
        return view('profile.user-profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile from user profile page
     */
    public function userProfileUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if this is a photo-only upload
        if ($request->has('photo_only') && $request->photo_only == '1') {
            // Only handle photo upload
            if ($file = $request->file('photo')) {
                $rules = ['photo' => 'image|file|max:5120'];
                $request->validate($rules);

                $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
                $path = 'public/profile/';

                // Delete old photo if exists
                if ($user->photo) {
                    Storage::delete($path . $user->photo);
                }

                // Store new photo
                $file->storeAs($path, $fileName);

                User::where('id', $user->id)->update(['photo' => $fileName]);

                return redirect()
                    ->to(shop_route('profile'))
                    ->with('success', 'Profile photo has been updated!');
            }

            return redirect()
                ->to(shop_route('profile'))
                ->with('error', 'No photo file selected.');
        }

        // Handle full profile update
        $rules = [
            'name' => 'required|max:50',
            'username' => 'required|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|max:20',
        ];

        // Add password validation if password is being changed
        if ($request->filled('password')) {
            $rules['current_password'] = 'required';
            $rules['password'] = 'required|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        // Check current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()
                    ->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }

            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // Handle photo upload
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/profile/';

            // Delete old photo if exists
            if ($user->photo) {
                Storage::delete($path . $user->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        User::where('id', $user->id)->update($validatedData);

        return redirect()
            ->to(shop_route('profile'))
            ->with('success', 'Profile has been updated!');
    }

    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if this is a photo-only upload
        if ($request->has('photo_only') && $request->photo_only == '1') {
            // Only handle photo upload
            if ($file = $request->file('photo')) {
                $rules = ['photo' => 'image|file|max:5120'];
                $request->validate($rules);

                $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
                $path = 'public/profile/';

                // Delete old photo if exists
                if ($user->photo) {
                    Storage::delete($path . $user->photo);
                }

                // Store new photo
                $file->storeAs($path, $fileName);

                User::where('id', $user->id)->update(['photo' => $fileName]);

                return redirect()
                    ->route('profile.edit')
                    ->with('success', 'Profile photo has been updated!');
            }

            return redirect()
                ->route('profile.edit')
                ->with('error', 'No photo file selected.');
        }

        // Handle regular profile update (name and username only, no email)
        $rules = [
            'name' => 'required|max:50',
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username,' . $user->id,
        ];

        $validatedData = $request->validate($rules);

        User::where('id', $user->id)->update($validatedData);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile has been updated!');
    }

    public function settings(Request $request)
    {
        $user = $request->user();
        $shop = $user->getActiveShop();

        // If user has a shop with a specific shop type, use shop-type specific view
        if ($shop && $shop->shop_type) {
            $shopType = shop_type_route_key($shop->shop_type->value);
            $shopTypeView = "shop-types.{$shopType}.profile.settings";

            if (view()->exists($shopTypeView)) {
                return view($shopTypeView, [
                    'user' => $user,
                    'shop' => $shop,
                ]);
            }
        }

        return view('profile.settings', [
            'user' => $user,
        ]);
    }

    public function features(Request $request)
    {
        $user = $request->user();
        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return redirect()->to(shop_route('settings'));
        }

        $shopType = shop_type_route_key($shop->shop_type->value);

        // Generic fallback: check for shop-type-specific view, else use shared view
        $shopTypeView = "shop-types.{$shopType}.profile.features";
        $layout = "shop-types.{$shopType}.layouts.nexora";
        if (view()->exists($shopTypeView)) {
            return view($shopTypeView, [
                'user' => $user,
                'shop' => $shop,
                'shopSettings' => $shop->shop_settings ?? [],
            ]);
        }

        return view('layouts.profile.features', [
            'user' => $user,
            'shop' => $shop,
            'shopSettings' => $shop->shop_settings ?? [],
            'layout' => $layout,
        ]);
    }

    /**
     * Generic shop settings update (for shop types without specific feature sets)
     */
    public function updateShopSettings(Request $request): RedirectResponse
    {
        $user = $request->user();
        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return redirect()->back()->with('error', 'Invalid shop');
        }

        return redirect()
            ->to(shop_route('features'))
            ->with('success', 'Settings saved!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->to('/');
    }
}
