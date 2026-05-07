<?php

namespace App\Policies;

use App\Models\User;
use App\ShopTypes\Tech\Models\TechRepairJob;

class TechRepairJobPolicy
{
    /**
     * Determine whether the user can view any tech repair jobs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_repair_jobs') || $user->can('view_orders');
    }

    /**
     * Determine whether the user can view the tech repair job.
     */
    public function view(User $user, TechRepairJob $techRepairJob): bool
    {
        return ($user->can('view_repair_jobs') || $user->can('view_orders')) &&
               $techRepairJob->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can create tech repair jobs.
     */
    public function create(User $user): bool
    {
        return $user->can('create_repair_jobs') || $user->can('create_orders');
    }

    /**
     * Determine whether the user can update the tech repair job.
     */
    public function update(User $user, TechRepairJob $techRepairJob): bool
    {
        // Allow technicians to update their own jobs
        if ($techRepairJob->assigned_technician_id === $user->id) {
            return true;
        }

        return ($user->can('edit_repair_jobs') || $user->can('edit_orders')) &&
               $techRepairJob->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can delete the tech repair job.
     */
    public function delete(User $user, TechRepairJob $techRepairJob): bool
    {
        return ($user->can('delete_repair_jobs') || $user->can('delete_orders')) &&
               $techRepairJob->shop_id === $user->currentShop->id;
    }

    /**
     * Determine whether the user can assign technicians.
     */
    public function assignTechnician(User $user, TechRepairJob $techRepairJob): bool
    {
        return $user->can('assign_repair_jobs') &&
               $techRepairJob->shop_id === $user->currentShop->id;
    }
}
