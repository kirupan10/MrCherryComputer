<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechProduct;

class TechProductPolicy
{
    /**
     * Determine whether the user can view any tech products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_products');
    }

    /**
     * Determine whether the user can view the tech product.
     */
    public function view(User $user, TechProduct $techProduct): bool
    {
        return $user->can('view_products') &&
               $techProduct->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can create tech products.
     */
    public function create(User $user): bool
    {
        return $user->can('create_products');
    }

    /**
     * Determine whether the user can update the tech product.
     */
    public function update(User $user, TechProduct $techProduct): bool
    {
        return !$user->isEmployee() &&
               $techProduct->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can delete the tech product.
     */
    public function delete(User $user, TechProduct $techProduct): bool
    {
        return $user->can('delete_products') &&
               $techProduct->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can restore the tech product.
     */
    public function restore(User $user, TechProduct $techProduct): bool
    {
        return $user->can('delete_products') &&
               $techProduct->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can permanently delete the tech product.
     */
    public function forceDelete(User $user, TechProduct $techProduct): bool
    {
        return $user->can('delete_products') &&
               $techProduct->shop_id === $user->currentShop->id;
    }
}
