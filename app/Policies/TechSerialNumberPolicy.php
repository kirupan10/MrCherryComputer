<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechSerialNumber;

class TechSerialNumberPolicy
{
    /**
     * Determine whether the user can view any tech serial numbers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_products');
    }

    /**
     * Determine whether the user can view the tech serial number.
     */
    public function view(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return $user->can('view_products') &&
               $techSerialNumber->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can create tech serial numbers.
     */
    public function create(User $user): bool
    {
        return $user->can('create_products');
    }

    /**
     * Determine whether the user can update the tech serial number.
     */
    public function update(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return $user->can('edit_products') &&
               $techSerialNumber->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can delete the tech serial number.
     */
    public function delete(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return $user->can('delete_products') &&
               $techSerialNumber->shop_id === $user->currentShop->id;
    }
}
