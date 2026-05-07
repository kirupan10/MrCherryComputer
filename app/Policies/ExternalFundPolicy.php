<?php

namespace App\Policies;

use App\Models\ExternalFund;
use App\Models\User;

class ExternalFundPolicy
{
    /**
     * Determine whether the user can view any external funds
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the external fund
     */
    public function view(User $user, ExternalFund $externalFund): bool
    {
        $activeShop = $user->getActiveShop();
        return $activeShop && $externalFund->shop_id === $activeShop->id;
    }

    /**
     * Determine whether the user can create external funds
     */
    public function create(User $user): bool
    {
        return $user->getActiveShop() !== null;
    }

    /**
     * Determine whether the user can update the external fund
     */
    public function update(User $user, ExternalFund $externalFund): bool
    {
        $activeShop = $user->getActiveShop();
        return $activeShop && $externalFund->shop_id === $activeShop->id;
    }

    /**
     * Determine whether the user can delete the external fund
     */
    public function delete(User $user, ExternalFund $externalFund): bool
    {
        $activeShop = $user->getActiveShop();
        return $activeShop && $externalFund->shop_id === $activeShop->id;
    }
}
