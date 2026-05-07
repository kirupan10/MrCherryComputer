<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, BelongsToShop;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'photo',
        'shop_id',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creditSales(): HasMany
    {
        return $this->hasMany(CreditSale::class);
    }

    public function scopeSearch($query, $value): void
    {
        if (empty($value)) {
            return;
        }

        // Remove spaces and special characters from search value for phone comparison
        $phoneSearch = preg_replace('/[\s\-\(\)]+/', '', $value);

        $query->where(function ($q) use ($value, $phoneSearch) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%")
              ->orWhere('address', 'like', "%{$value}%")
              ->orWhere('phone', 'like', "%{$value}%");

            // Additional phone search without formatting if different from original
            if ($phoneSearch !== $value && !empty($phoneSearch)) {
                $q->orWhere('phone', 'like', "%{$phoneSearch}%");
            }
        });
    }

    /**
     * Scope to exclude Walk-In Customer from queries
     */
    public function scopeExcludeWalkIn($query)
    {
        return $query->where('name', '!=', 'Walk-In Customer');
    }

    /**
     * Check if this customer is the Walk-In Customer
     */
    public function isWalkInCustomer(): bool
    {
        return $this->name === 'Walk-In Customer';
    }

    /**
     * Get or create the default Walk-In Customer for the current shop
     * Ensures only ONE Walk-In Customer record per shop
     */
    public static function getOrCreateWalkInCustomer($shopId = null): self
    {
        if (!$shopId) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $shopId = $user ? $user->shop_id : null;
        }

        return self::firstOrCreate(
            [
                'name' => 'Walk-In Customer',
                'shop_id' => $shopId
            ],
            [
                'phone' => null,
                'email' => null,
                'address' => null,
                'created_by' => \Illuminate\Support\Facades\Auth::id()
            ]
        );
    }
}
