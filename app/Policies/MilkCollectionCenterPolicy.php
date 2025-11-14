<?php

namespace App\Policies;

use App\Models\MilkCollectionCenter;
use App\Models\User;

class MilkCollectionCenterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MilkCollectionCenter $milkCollectionCenter): bool
    {
        // Super admin and admin can view all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // User role can view all (view-only)
        if ($user->isUser()) {
            return true;
        }

        // Partner can view their MCCs
        if ($user->isPartner() && $user->partner_id) {
            return $milkCollectionCenter->partner_id === $user->partner_id;
        }

        // MCC users can view their own center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $milkCollectionCenter->id === $user->milk_collection_center_id;
        }

        // Agent can view their MCC
        if ($user->agent && $user->agent->milk_collection_center_id) {
            return $milkCollectionCenter->id === $user->agent->milk_collection_center_id;
        }

        // Agent from partner can view partner's MCCs
        if ($user->agent && $user->agent->partner_id) {
            return $milkCollectionCenter->partner_id === $user->agent->partner_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only super admin, admin, and partners can create MCCs
        return $user->isAdminOrSuperAdmin() || $user->isPartner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MilkCollectionCenter $milkCollectionCenter): bool
    {
        // Super admin and admin can update all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // Partner can update their MCCs
        if ($user->isPartner() && $user->partner_id) {
            return $milkCollectionCenter->partner_id === $user->partner_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MilkCollectionCenter $milkCollectionCenter): bool
    {
        // Only super admin and admin can delete
        return $user->isAdminOrSuperAdmin();
    }
}
