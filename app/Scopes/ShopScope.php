<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ShopScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * This ensures multi-tenancy by filtering queries to only show records from the user's shop.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            // If not authenticated, restrict access (prevent unauthenticated access to orders)
            $builder->whereNull($model->getTable() . '.shop_id');
            return;
        }

        $user = Auth::user();

        // Skip filtering for admin users (they need to see all shops' data)
        if ($user && $user->isAdmin()) {
            return;
        }

        // Get the active shop for filtering
        $activeShop = $user->getActiveShop();
        if ($activeShop) {
            // Apply shop scope to filter by user's active shop
            $builder->where($model->getTable() . '.shop_id', $activeShop->id);
        } else {
            // User has no active shop - restrict to null shop_id (prevents data leakage)
            $builder->whereNull($model->getTable() . '.shop_id');
        }
    }
}
