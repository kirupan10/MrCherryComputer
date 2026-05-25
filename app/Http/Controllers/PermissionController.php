<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ShopPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Only shop owners and managers can manage permissions.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user instanceof User || $user->isEmployee()) {
                abort(403, 'Only shop owners and managers can manage permissions.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user instanceof User) {
            abort(403, 'Unauthorized user context.');
        }

        $user->loadMissing(['shop', 'ownedShop']);
        $shop = $user->getActiveShop();

        if (!$shop) {
            abort(404, 'No active shop found.');
        }

        $activeShopType = $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : null;
        if (!$activeShopType) {
            abort(404, 'No active shop type found.');
        }

        $permissions = ShopPermission::forShop($shop->id);
        $definitions = ShopPermission::PERMISSIONS;

        $viewCandidates = [
            'permissions.index',
        ];

        foreach ($viewCandidates as $viewName) {
            if (view()->exists($viewName)) {
                return view($viewName, compact('shop', 'permissions', 'definitions'));
            }
        }

        abort(404, 'Permissions view not found for active shop type.');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user instanceof User) {
            abort(403, 'Unauthorized user context.');
        }

        $user->loadMissing(['shop', 'ownedShop']);
        $shop = $user->getActiveShop();

        if (!$shop) {
            abort(404, 'No active shop found.');
        }

        $activeShopType = $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : null;
        if (!$activeShopType) {
            abort(404, 'No active shop type found.');
        }

        // Only shop owners can modify manager permissions
        if ($user->isManagerRole() || $user->isManager()) {
            // Managers can only update employee permissions
            $allowed = ['employee'];
        } else {
            $allowed = ['manager', 'employee'];
        }

        $data = [];

        foreach ($allowed as $role) {
            $data[$role] = [];
            foreach (ShopPermission::PERMISSIONS as $group) {
                foreach (array_keys($group) as $permission) {
                    // Checkbox: present = true, absent = false
                    $data[$role][$permission] = $request->boolean("permissions.{$role}.{$permission}");
                }
            }
        }

        ShopPermission::saveForShop($shop->id, $data);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permissions updated successfully.');
    }
}
