<?php

namespace App\Models;

use App\Enums\ShopType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \App\Enums\ShopType|string $shop_type
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $owner_id
 * @property bool $is_active
 * @property bool $is_suspended
 * @property string|float|int|null $monthly_fee
 * @property string|null $subscription_status
 * @property \Illuminate\Support\Carbon|null $subscription_end_date
 */
class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shop_type',
        'enabled_features',
        'address',
        'phone',
        'email',
        'owner_id',
        'is_active',
        'letterhead_config',
        'job_letterhead_config',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_status',
        'is_suspended',
        'suspended_at',
        'suspended_by',
        'suspension_reason'
    ];

    protected $casts = [
        'shop_type' => ShopType::class,
        'enabled_features' => 'array',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'letterhead_config' => 'array',
        'job_letterhead_config' => 'array',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'suspended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'shop_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shop_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'shop_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'shop_id');
    }

    // Helper methods
    public function isOwnedBy(User $user)
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Check if shop has a specific feature enabled
     */
    public function hasFeature(string $feature): bool
    {
        // Get default features for this shop type
        $defaultFeatures = config("shop-features.{$this->shop_type->value}", []);

        // If enabled_features is set, use it; otherwise use defaults
        $enabledFeatures = $this->enabled_features ?? $defaultFeatures;

        return in_array($feature, $enabledFeatures);
    }

    /**
     * Get all enabled features for this shop
     */
    public function getEnabledFeatures(): array
    {
        if ($this->enabled_features) {
            return $this->enabled_features;
        }

        return config("shop-features.{$this->shop_type->value}", []);
    }

    /**
     * Get shop type label
     */
    public function getShopTypeLabel(): string
    {
        return $this->shop_type->label();
    }

    /**
     * Get shop type icon
     */
    public function getShopTypeIcon(): string
    {
        return $this->shop_type->icon();
    }
}
