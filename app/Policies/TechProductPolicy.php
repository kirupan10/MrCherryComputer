<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechProduct;

class TechProductPolicy
{
    /**
     * True if the user has access to the shop that owns the product.
     */
    private function userOwnsShop(User $user, TechProduct $techProduct): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Check active shop first (fast path)
        $shop = $user->getActiveShop();
        if ($shop && (int) $techProduct->shop_id === (int) $shop->id) {
            return true;
        }

        // Fall back to all owned/assigned shops
        return $user->ownedShops()->where('shops.id', $techProduct->shop_id)->exists();
    }

    /**
     * Determine whether the user can view any tech products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    /**
     * Determine whether the user can view the tech product.
     */
    public function view(User $user, TechProduct $techProduct): bool
    {
        return $user->hasInventoryAccess() && $this->userOwnsShop($user, $techProduct);
    }

    /**
     * Determine whether the user can create tech products.
     */
    public function create(User $user): bool
    {
        return !$user->isEmployee();
    }

    /**
     * Determine whether the user can update the tech product.
     */
    public function update(User $user, TechProduct $techProduct): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techProduct);
    }

    /**
     * Determine whether the user can delete the tech product.
     */
    public function delete(User $user, TechProduct $techProduct): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techProduct);
    }

    /**
     * Determine whether the user can restore the tech product.
     */
    public function restore(User $user, TechProduct $techProduct): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techProduct);
    }

    /**
     * Determine whether the user can permanently delete the tech product.
     */
    public function forceDelete(User $user, TechProduct $techProduct): bool
    {
        return $user->isAdmin() && $this->userOwnsShop($user, $techProduct);
    }
}
