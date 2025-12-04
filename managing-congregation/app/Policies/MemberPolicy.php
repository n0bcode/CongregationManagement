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
        return $user->hasPermission('members.view');
    }

    public function view(User $user, Member $member): bool
    {
        if (! $user->hasPermission('members.view')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $member->community_id;
        }

        // General and Super Admin can view all
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('members.create');
    }

    public function update(User $user, Member $member): bool
    {
        if (! $user->hasPermission('members.edit')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $member->community_id;
        }

        // General and Super Admin can update all
        return true;
    }

    public function delete(User $user, Member $member): bool
    {
        if (! $user->hasPermission('members.delete')) {
            return false;
        }

        // Community scoping for Directors (if they had delete permission)
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $member->community_id;
        }

        return true;
    }

    public function restore(User $user, Member $member): bool
    {
        return $user->hasPermission('members.delete');
    }

    public function forceDelete(User $user, Member $member): bool
    {
        return $user->hasPermission('members.delete');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('members.export');
    }
}
