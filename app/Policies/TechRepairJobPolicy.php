<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechRepairJob;

class TechRepairJobPolicy
{
    private function userOwnsShop(User $user, TechRepairJob $techRepairJob): bool
    {
        if ($user->isAdmin()) return true;
        $shop = $user->getActiveShop();
        if ($shop && (int) $techRepairJob->shop_id === (int) $shop->id) return true;
        return $user->ownedShops()->where('shops.id', $techRepairJob->shop_id)->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    public function view(User $user, TechRepairJob $techRepairJob): bool
    {
        return $user->hasInventoryAccess() && $this->userOwnsShop($user, $techRepairJob);
    }

    public function create(User $user): bool
    {
        return $user->hasInventoryAccess();
    }

    public function update(User $user, TechRepairJob $techRepairJob): bool
    {
        if ($techRepairJob->assigned_technician_id === $user->id) {
            return true;
        }
        return !$user->isEmployee() && $this->userOwnsShop($user, $techRepairJob);
    }

    public function delete(User $user, TechRepairJob $techRepairJob): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techRepairJob);
    }

    public function assignTechnician(User $user, TechRepairJob $techRepairJob): bool
    {
        return !$user->isEmployee() && $this->userOwnsShop($user, $techRepairJob);
    }
}
