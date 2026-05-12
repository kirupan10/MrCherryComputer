<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechSerialNumber;

class TechSerialNumberPolicy
{
    private function userOwnsShop(User $user, TechSerialNumber $techSerialNumber): bool
    {
        if ($user->isAdmin()) return true;
        $shop = $user->getActiveShop();
        if ($shop && (int) $techSerialNumber->shop_id === (int) $shop->id) return true;
        return $user->ownedShops()->where('shops.id', $techSerialNumber->shop_id)->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    public function view(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return $user->hasInventoryAccess() && $this->userOwnsShop($user, $techSerialNumber);
    }

    public function create(User $user): bool
    {
        return !$user->isEmployee();
    }

    public function update(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techSerialNumber);
    }

    public function delete(User $user, TechSerialNumber $techSerialNumber): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techSerialNumber);
    }
}
