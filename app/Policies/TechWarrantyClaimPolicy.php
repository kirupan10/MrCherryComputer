<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechWarrantyClaim;

class TechWarrantyClaimPolicy
{
    private function userOwnsShop(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        if ($user->isAdmin()) return true;
        $shop = $user->getActiveShop();
        if ($shop && (int) $techWarrantyClaim->shop_id === (int) $shop->id) return true;
        return $user->ownedShops()->where('shops.id', $techWarrantyClaim->shop_id)->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    public function view(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return $user->hasInventoryAccess() && $this->userOwnsShop($user, $techWarrantyClaim);
    }

    public function create(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    public function update(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techWarrantyClaim);
    }

    public function delete(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techWarrantyClaim);
    }

    public function approve(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techWarrantyClaim);
    }

    public function reject(User $user, TechWarrantyClaim $techWarrantyClaim): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techWarrantyClaim);
    }
}
