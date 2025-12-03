<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::GENERAL) || $user->hasRole(UserRole::DIRECTOR);
    }

    public function view(User $user, Member $member): bool
    {
        if ($user->hasRole(UserRole::GENERAL)) {
            return true;
        }

        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $member->community_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::GENERAL) || $user->hasRole(UserRole::DIRECTOR);
    }

    public function update(User $user, Member $member): bool
    {
        if ($user->hasRole(UserRole::GENERAL)) {
            return true;
        }

        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $member->community_id;
        }

        return false;
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->hasRole(UserRole::GENERAL);
    }

    public function restore(User $user, Member $member): bool
    {
        return $user->hasRole(UserRole::GENERAL);
    }

    public function forceDelete(User $user, Member $member): bool
    {
        return $user->hasRole(UserRole::GENERAL);
    }
}
