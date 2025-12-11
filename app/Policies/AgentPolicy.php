<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;

class AgentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super admin, admin, partner, and mcc can view agents
        return in_array($user->user_type, ['super_admin', 'admin', 'partner', 'mcc']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Agent $agent): bool
    {
        // Super admin and admin can view all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // Partner can view agents from their organization
        if ($user->isPartner() && $user->partner_id) {
            return $agent->partner_id === $user->partner_id;
        }

        // MCC can view agents from their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $agent->milk_collection_center_id === $user->milk_collection_center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super admin, admin, partner, and mcc can create agents
        return in_array($user->user_type, ['super_admin', 'admin', 'partner', 'mcc']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Agent $agent): bool
    {
        // Super admin and admin can update all
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        // Partner can update agents from their organization
        if ($user->isPartner() && $user->partner_id) {
            return $agent->partner_id === $user->partner_id;
        }

        // MCC can update agents from their center
        if ($user->isMcc() && $user->milk_collection_center_id) {
            return $agent->milk_collection_center_id === $user->milk_collection_center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Agent $agent): bool
    {
        // Only super admin and admin can delete
        return $user->isAdminOrSuperAdmin();
    }
}
