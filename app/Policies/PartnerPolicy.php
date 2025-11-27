<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PartnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Partner $partner): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->isPartner() && $user->partner_id === $partner->id;
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Partner $partner): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $user->isPartner() && $user->partner_id === $partner->id;
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Partner $partner): bool
    {
        return false;
    }

    public function forceDelete(User $user, Partner $partner): bool
    {
        return false;
    }

    protected function isAdmin(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
