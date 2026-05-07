<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int|null $shop_id
 * @property bool $is_suspended
 * @property string|null $suspension_reason
 * @property string|null $suspension_type
 * @property int|null $suspension_duration
 * @property \Illuminate\Support\Carbon|null $suspended_at
 * @property \Illuminate\Support\Carbon|null $suspension_ends_at
 * @property int|null $suspended_by
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Role constants
    const ROLE_ADMIN = 'admin'; // Main admin role
    const ROLE_SHOP_OWNER = 'shop_owner';
    const ROLE_MANAGER = 'manager';
    const ROLE_EMPLOYEE = 'employee';

    protected $fillable = [
        'photo',
        'name',
        'username',
        'email',
        'password',
        'role',
        'shop_id',
        'is_suspended',
        'suspension_reason',
        'suspension_type',
        'suspension_duration',
        'suspended_at',
        'suspension_ends_at',
        'suspended_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_suspended' => 'boolean',
        'suspended_at' => 'datetime',
        'suspension_ends_at' => 'datetime',
    ];

    protected $with = ['shop'];

    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%");
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }


    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        // For backward compatibility - admin is the highest role
        return $this->isAdmin();
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    // New role-based helper methods
    public function isShopOwner(): bool
    {
        return $this->role === self::ROLE_SHOP_OWNER;
    }

    public function isManagerRole(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function hasInventoryAccess(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER, self::ROLE_MANAGER, self::ROLE_EMPLOYEE]);
    }

    public function hasFullAccess(): bool
    {
        return $this->isAdmin();
    }

    public function canAccessReports(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER, self::ROLE_MANAGER]);
    }

    public function canEditOrders(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER, self::ROLE_MANAGER]);
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER]);
    }

    public function canManageAllUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canCreateShops(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER]);
    }

    // Staff-specific permission methods
    public function canAccessFinance(): bool
    {
        return !$this->isEmployee();
    }

    public function canAccessFinanceDashboard(): bool
    {
        return !$this->isEmployee();
    }

    public function canAccessTransactions(): bool
    {
        return !$this->isEmployee();
    }

    public function canEditFinanceRecords(): bool
    {
        return !$this->isEmployee();
    }

    public function canAccessDataImport(): bool
    {
        return !$this->isEmployee();
    }

    public function canAccessAuditLogs(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_OWNER, self::ROLE_MANAGER]);
    }

    public function canAccessLetterhead(): bool
    {
        return !$this->isEmployee();
    }

    public function canSeeDashboardOverview(): bool
    {
        return !$this->isEmployee();
    }

    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_SHOP_OWNER => 'Shop Owner',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_EMPLOYEE => 'Employee',
            default => 'Unknown'
        };
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_SHOP_OWNER => 'Shop Owner',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_EMPLOYEE => 'Employee',
        ];
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Multi-user relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creditSales()
    {
        return $this->hasMany(CreditSale::class);
    }

    public function creditPayments()
    {
        return $this->hasMany(CreditPayment::class);
    }

    // Shop relationship
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function ownedShop()
    {
        return $this->hasOne(Shop::class, 'owner_id');
    }

    public function ownedShops()
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    // Suspension relationship
    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    // Helper methods for shop management
    public function hasShop()
    {
        // Admin can work independently without a shop
        if ($this->isAdmin()) {
            return true;
        }
        return $this->shop_id !== null || $this->role === self::ROLE_SHOP_OWNER;
    }

    public function getActiveShop(): ?Shop
    {
        // Admin can access any shop, return the first available shop only when needed
        if ($this->isAdmin()) {
            // Return the first available shop, but don't create one automatically
            return Shop::where('is_active', true)->first();
        }

        if ($this->role === self::ROLE_SHOP_OWNER) {
            // Prefer the persisted current shop_id for single-shop operation.
            if ($this->shop_id) {
                $currentShop = $this->ownedShops()
                    ->where('id', $this->shop_id)
                    ->where('is_active', true)
                    ->first();
                if ($currentShop) {
                    return $currentShop;
                }
            }

            // Fallback to the first active owned shop and persist it as the current shop.
            $fallbackShop = $this->ownedShops()
                ->where('is_active', true)
                ->orderBy('id')
                ->first();

            if ($fallbackShop && $this->shop_id !== $fallbackShop->id) {
                $this->forceFill(['shop_id' => $fallbackShop->id])->saveQuietly();
            }

            return $fallbackShop;
        }

        // For manager/employee, avoid triggering lazy loading on the shop relation.
        if (!$this->shop_id) {
            return null;
        }

        if ($this->relationLoaded('shop')) {
            $shop = $this->getRelation('shop');
            return $shop && $shop->is_active ? $shop : null;
        }

        return Shop::whereKey($this->shop_id)
            ->where('is_active', true)
            ->first();
    }



    public function isInShop(int|string $shopId): bool
    {
        if ($this->role === self::ROLE_SHOP_OWNER) {
            return $this->ownedShops()
                ->whereKey($shopId)
                ->exists();
        }
        return $this->shop_id == $shopId;
    }

    public function canAccessShop(int|string $shopId): bool
    {
        // Admin can access any shop
        if ($this->isAdmin()) {
            return true;
        }

        // Shop owners can access their own shop
        if ($this->role === self::ROLE_SHOP_OWNER) {
            return $this->ownedShops()
                ->whereKey($shopId)
                ->exists();
        }

        // Employees and managers can access their assigned shop
        if (in_array($this->role, [self::ROLE_EMPLOYEE, self::ROLE_MANAGER])) {
            return $this->shop_id == $shopId;
        }

        return false;
    }

    /**
     * Check if user owns multiple shops
     */
    public function ownsMultipleShops()
    {
        if (!$this->isShopOwner()) {
            return false;
        }
        return $this->ownedShops()->where('is_active', true)->count() > 1;
    }

    /**
     * Get all active shops owned by this user
     */
    public function getOwnedShops()
    {
        if (!$this->isShopOwner()) {
            return collect();
        }
        return $this->ownedShops()->where('is_active', true)->get();
    }

    /**
     * Get shop-type-aware view name with fallback to tech
     *
     * @param string $viewPath The base view path (e.g., 'products.index', 'orders.create')
     * @param string|null $fallback Optional fallback view if shop-type view doesn't exist
     * @return string The resolved view name
     */
    public function getShopTypeView(string $viewPath, ?string $fallback = null): string
    {
        $shop = $this->getActiveShop();
        $shopType = $shop && $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : 'tech';

        // Try shop-type-specific view first
        $shopTypeView = "shop-types.{$shopType}.{$viewPath}";
        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        // Try tech fallback view
        $techView = "shop-types.tech.{$viewPath}";
        if (view()->exists($techView)) {
            return $techView;
        }

        // Return fallback if provided, otherwise return the original path
        return $fallback ?? $viewPath;
    }

    /**
     * Return a shop-type-aware view with data
     *
     * @param string $viewPath The base view path (e.g., 'products.index', 'orders.create')
     * @param array $data Data to pass to the view
     * @param string|null $fallback Optional fallback view if shop-type view doesn't exist
     * @return \Illuminate\View\View
     */
    public function viewForShopType(string $viewPath, array $data = [], ?string $fallback = null)
    {
        $viewName = $this->getShopTypeView($viewPath, $fallback);

        // Add shopType to data for view usage
        $shop = $this->getActiveShop();
        $data['shopType'] = $shop && $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : 'tech';

        return view($viewName, $data);
    }

    /**
     * Check a named shop permission for this user.
     * Admin and shop_owner always return true.
     */
    public function hasShopPermission(string $permission): bool
    {
        if ($this->isAdmin() || $this->isShopOwner()) {
            return true;
        }

        $shop = $this->getActiveShop();
        if (!$shop) {
            // Fall back to role-based default when no shop
            return !$this->isEmployee();
        }

        return \App\Models\ShopPermission::check($shop->id, $this->role, $permission);
    }

}
