<?php

namespace App\Traits;

use App\Scopes\ShopScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToShop
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToShop(): void
    {
        // Apply the shop scope to all queries
        static::addGlobalScope(new ShopScope);

        // Automatically assign shop_id and created_by when creating records
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $user = Auth::user();
                $table = $model->getTable();

                // Set created_by to current user
                if (Schema::hasColumn($table, 'created_by') && !$model->created_by) {
                    $model->created_by = $user->id;
                }

                // Set shop_id based on user's active shop
                if (Schema::hasColumn($table, 'shop_id') && !$model->shop_id && $user->getActiveShop()) {
                    $model->shop_id = $user->getActiveShop()->id;
                }
            }
        });
    }

    /**
     * Get the shop that owns this model
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    /**
     * Get the user who created this model
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope to get records without shop filtering (admin use)
     */
    public function scopeWithoutShopScope($query)
    {
        return $query->withoutGlobalScope(ShopScope::class);
    }

    /**
     * Scope records for a specific shop ID.
     */
    public function scopeForShop($query, int $shopId)
    {
        return $query->withoutGlobalScope(ShopScope::class)
            ->where('shop_id', $shopId);
    }
}
