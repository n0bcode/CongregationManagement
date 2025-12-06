<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Community;
use App\Models\User;

class CommunityPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any communities.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'communities.index');
    }

    /**
     * Determine whether the user can view the community.
     */
    public function view(User $user, Community $community): bool
    {
        return $this->hasPermission($user, 'communities.show');
    }

    /**
     * Determine whether the user can create communities.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'communities.create');
    }

    /**
     * Determine whether the user can update the community.
     */
    public function update(User $user, Community $community): bool
    {
        return $this->hasPermission($user, 'communities.edit');
    }

    /**
     * Determine whether the user can delete the community.
     */
    public function delete(User $user, Community $community): bool
    {
        return $this->hasPermission($user, 'communities.delete');
    }

    /**
     * Determine whether the user can restore the community.
     */
    public function restore(User $user, Community $community): bool
    {
        return $this->hasPermission($user, 'communities.delete');
    }

    /**
     * Determine whether the user can permanently delete the community.
     */
    public function forceDelete(User $user, Community $community): bool
    {
        return $this->hasPermission($user, 'communities.delete');
    }
}
