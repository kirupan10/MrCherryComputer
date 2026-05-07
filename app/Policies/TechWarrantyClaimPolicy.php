<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechWarrantyClaim;

class TechWarrantyClaimPolicy
{
    /**
     * Determine whether the user can view any tech warranty claims.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_warranty_claims') || $user->can('view_orders');
    }

    /**
     * Determine whether the user can view the tech warranty claim.
     */
    public function view(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return ($user->can('view_warranty_claims') || $user->can('view_orders')) &&
               $techWarrantyClaim->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can create tech warranty claims.
     */
    public function create(User $user): bool
    {
        return $user->can('create_warranty_claims') || $user->can('create_orders');
    }

    /**
     * Determine whether the user can update the tech warranty claim.
     */
    public function update(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return ($user->can('edit_warranty_claims') || $user->can('edit_orders')) &&
               $techWarrantyClaim->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can delete the tech warranty claim.
     */
    public function delete(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return ($user->can('delete_warranty_claims') || $user->can('delete_orders')) &&
               $techWarrantyClaim->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can approve the warranty claim.
     */
    public function approve(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return $user->can('approve_warranty_claims') &&
               $techWarrantyClaim->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can reject the warranty claim.
     */
    public function reject(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return $user->can('approve_warranty_claims') &&
               $techWarrantyClaim->shop_id === $user->currentShop->id;
    }
}
