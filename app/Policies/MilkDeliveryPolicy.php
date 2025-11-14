<?php

namespace App\Policies;

use App\Models\MilkDelivery;
use App\Models\User;

class MilkDeliveryPolicy
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
    public function view(User $user, MilkDelivery $milkDelivery): bool
    {
        // Super admin and admin can view all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // User role can view all (view-only)
        if ($user->isUser()) {
            return true;
        }

        // Partner can view deliveries from their MCCs
        if ($user->isPartner() && $user->partner_id) {
            $mccIds = $user->partner->milkCollectionCenters()->pluck('id')->toArray();
            return in_array($milkDelivery->milk_collection_center_id, $mccIds);
        }

        // MCC can view deliveries to their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $milkDelivery->milk_collection_center_id === $user->milk_collection_center_id;
        }

        // Agent can view deliveries from their MCC/Partner
        if ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                return $milkDelivery->milk_collection_center_id === $agent->milk_collection_center_id;
            }
            if ($agent->partner_id) {
                $mccIds = $agent->partner->milkCollectionCenters()->pluck('id')->toArray();
                return in_array($milkDelivery->milk_collection_center_id, $mccIds);
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

        // MCC users can create deliveries for their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return true;
        }

        // Agents can create deliveries
        if ($user->agent) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MilkDelivery $milkDelivery): bool
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

        // MCC can update deliveries to their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $milkDelivery->milk_collection_center_id === $user->milk_collection_center_id;
        }

        // Agent can update deliveries from their MCC/Partner
        if ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                return $milkDelivery->milk_collection_center_id === $agent->milk_collection_center_id;
            }
            if ($agent->partner_id) {
                $mccIds = $agent->partner->milkCollectionCenters()->pluck('id')->toArray();
                return in_array($milkDelivery->milk_collection_center_id, $mccIds);
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MilkDelivery $milkDelivery): bool
    {
        // Only super admin and admin can delete
        return $user->isAdminOrSuperAdmin();
    }
}
