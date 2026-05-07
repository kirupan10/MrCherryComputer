<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ShopPermission extends Model
{
    protected $fillable = ['shop_id', 'role', 'permission', 'granted'];

    protected $casts = ['granted' => 'boolean'];

    // ──────────────────────────────────────────────
    // All available permissions with defaults per role
    // ──────────────────────────────────────────────
    public const PERMISSIONS = [
        'products' => [
            'edit_product'   => ['label' => 'Edit product details',   'manager' => true,  'employee' => false],
            'add_product'    => ['label' => 'Add new products',        'manager' => true,  'employee' => false],
            'delete_product' => ['label' => 'Delete products',         'manager' => true,  'employee' => false],
            'add_stock'      => ['label' => 'Add new stock',           'manager' => true,  'employee' => true],
        ],
        'sales' => [
            'create_sale'    => ['label' => 'Create new sales',        'manager' => true,  'employee' => true],
            'edit_sale'      => ['label' => 'Edit sales / orders',     'manager' => true,  'employee' => false],
            'delete_sale'    => ['label' => 'Delete / void sales',     'manager' => true,  'employee' => false],
            'apply_discount' => ['label' => 'Apply discounts',         'manager' => true,  'employee' => true],
        ],
        'customers' => [
            'manage_customers' => ['label' => 'Manage customers',      'manager' => true,  'employee' => true],
        ],
        'finance' => [
            'view_finance'     => ['label' => 'View finance data',     'manager' => true,  'employee' => false],
            'manage_expenses'  => ['label' => 'Manage expenses',       'manager' => true,  'employee' => false],
        ],
        'reports' => [
            'view_reports'     => ['label' => 'View reports',          'manager' => true,  'employee' => false],
        ],
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    // ──────────────────────────────────────────────
    // Static helpers
    // ──────────────────────────────────────────────

    /**
     * Check whether a given role has a permission for the given shop.
     * Falls back to the default defined in PERMISSIONS when no DB row exists.
     */
    public static function check(int $shopId, string $role, string $permission): bool
    {
        if (!in_array($role, ['manager', 'employee'])) {
            // Admin and shop_owner always have full access
            return true;
        }

        $cacheKey = "shop_perm:{$shopId}:{$role}:{$permission}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($shopId, $role, $permission) {
            $row = static::where('shop_id', $shopId)
                ->where('role', $role)
                ->where('permission', $permission)
                ->first();

            if ($row !== null) {
                return (bool) $row->granted;
            }

            // Fall back to default
            foreach (static::PERMISSIONS as $group) {
                if (isset($group[$permission])) {
                    return (bool) $group[$permission][$role];
                }
            }

            return false;
        });
    }

    /**
     * Save permissions from a batch array and clear the cache.
     * $data = ['manager' => ['edit_product' => true, ...], 'employee' => [...]]
     */
    public static function saveForShop(int $shopId, array $data): void
    {
        foreach ($data as $role => $permissions) {
            if (!in_array($role, ['manager', 'employee'])) {
                continue;
            }
            foreach ($permissions as $permission => $granted) {
                static::updateOrCreate(
                    ['shop_id' => $shopId, 'role' => $role, 'permission' => $permission],
                    ['granted' => (bool) $granted]
                );
                Cache::forget("shop_perm:{$shopId}:{$role}:{$permission}");
            }
        }
    }

    /**
     * Return all permissions for a shop indexed by [role][permission] => granted.
     */
    public static function forShop(int $shopId): array
    {
        $rows = static::where('shop_id', $shopId)->get()->keyBy(fn ($r) => "{$r->role}.{$r->permission}");
        $result = [];

        foreach (['manager', 'employee'] as $role) {
            foreach (static::PERMISSIONS as $group) {
                foreach ($group as $key => $defaults) {
                    $rowKey = "{$role}.{$key}";
                    $result[$role][$key] = isset($rows[$rowKey])
                        ? (bool) $rows[$rowKey]->granted
                        : (bool) $defaults[$role];
                }
            }
        }

        return $result;
    }
}
