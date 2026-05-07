<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'duration',
        'years',
        'shop_id',
    ];

    /**
     * The "booted" method of the model.
     * Apply global scope to filter warranties by current shop
     */
    protected static function booted(): void
    {
        static::addGlobalScope('shop', function (Builder $builder) {
            $user = auth()->user();
            $shopId = $user instanceof User ? $user->getActiveShop()?->id : null;

            if ($shopId) {
                $builder->where('shop_id', $shopId);
            } else {
                // If no shop found, return no results
                $builder->whereRaw('1 = 0');
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include warranties for the current shop.
     */
    public function scopeForCurrentShop(Builder $query): Builder
    {
        $user = auth()->user();
        $shopId = $user instanceof User ? $user->getActiveShop()?->id : null;

        if ($shopId) {
            return $query->where('shop_id', $shopId);
        }

        // If no shop is found, return empty result instead of all warranties
        return $query->whereRaw('1 = 0');
    }

    /**
     * Scope a query to only include warranties for a specific shop.
     */
    public function scopeForShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }
}
