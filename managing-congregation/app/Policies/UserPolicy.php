<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Perform pre-authorization checks.
     * Super admin bypass pattern.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::GENERAL);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN)
            || $user->hasRole(UserRole::GENERAL)
            || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::GENERAL);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN)
            || $user->hasRole(UserRole::GENERAL)
            || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::GENERAL);
    }
}
