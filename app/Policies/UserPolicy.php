<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only super admin can view users
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Only super admin can view users
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only super admin can create users (admins and super admins)
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Only super admin can update users
        // Super admin cannot update themselves to prevent lockout
        if ($user->isSuperAdmin() && $user->id !== $model->id) {
            return true;
        }

        // Super admin can activate/deactivate any user including themselves
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // No one can delete users (as per requirements)
        return false;
    }

    /**
     * Determine whether the user can activate/deactivate the model.
     */
    public function toggleActive(User $user, User $model): bool
    {
        // Only super admin can activate/deactivate users
        // Cannot deactivate themselves
        return $user->isSuperAdmin() && $user->id !== $model->id;
    }
}
