<?php

namespace App\Policies;

use App\Models\Farmer;
use App\Models\User;

class FarmerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super admin, admin, partner, mcc, and user can view
        return in_array($user->user_type, ['super_admin', 'admin', 'partner', 'mcc', 'user']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Farmer $farmer): bool
    {
        // Super admin and admin can view all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // User role is view-only for all
        if ($user->isUser()) {
            return true;
        }

        // Partner can view farmers from their MCCs
        if ($user->isPartner() && $user->partner_id) {
            $mccIds = $user->partner->milkCollectionCenters()->pluck('id')->toArray();
            return in_array($farmer->milk_collection_center_id, $mccIds);
        }

        // MCC can view farmers attached to their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $farmer->milk_collection_center_id === $user->milk_collection_center_id;
        }

        // Agent can view farmers from their MCC/Partner
        if ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                return $farmer->milk_collection_center_id === $agent->milk_collection_center_id;
            }
            if ($agent->partner_id) {
                $mccIds = $agent->partner->milkCollectionCenters()->pluck('id')->toArray();
                return in_array($farmer->milk_collection_center_id, $mccIds);
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admin, admin, mcc, and agents can create
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // MCC users can create farmers for their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return true;
        }

        // Agents can create farmers
        if ($user->agent) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farmer $farmer): bool
    {
        // Super admin and admin can update all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // User role is view-only
        if ($user->isUser()) {
            return false;
        }

        // Partner cannot update (view-only)
        if ($user->isPartner()) {
            return false;
        }

        // MCC can update farmers attached to their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $farmer->milk_collection_center_id === $user->milk_collection_center_id;
        }

        // Agent can update farmers from their MCC/Partner
        if ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                return $farmer->milk_collection_center_id === $agent->milk_collection_center_id;
            }
            if ($agent->partner_id) {
                $mccIds = $agent->partner->milkCollectionCenters()->pluck('id')->toArray();
                return in_array($farmer->milk_collection_center_id, $mccIds);
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farmer $farmer): bool
    {
        // Only super admin and admin can delete
        return $user->isAdminOrSuperAdmin();
    }
}
