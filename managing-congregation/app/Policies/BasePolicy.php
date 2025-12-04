<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

abstract class BasePolicy
{
    /**
     * Perform pre-authorization checks.
     * Super Admin bypass for all actions.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Check if user has permission
     *
     * Includes error handling with graceful degradation
     */
    protected function hasPermission(User $user, string $permission): bool
    {
        try {
            return $user->hasPermission($permission);
        } catch (\Throwable $e) {
            // Log error and deny access on failure (fail-safe)
            \Log::error('Permission check failed in policy', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage(),
            ]);

            // Fail-safe: deny access on error
            return false;
        }
    }

    /**
     * Check if user belongs to the same community as the model
     */
    protected function isSameCommunity(User $user, $model): bool
    {
        if (! isset($model->community_id)) {
            return true; // No community restriction
        }

        return $user->community_id === $model->community_id;
    }

    /**
     * Check if user is Director with community scoping
     */
    protected function isDirectorWithCommunityAccess(User $user, $model): bool
    {
        if (! $user->hasRole(UserRole::DIRECTOR)) {
            return false;
        }

        return $this->isSameCommunity($user, $model);
    }

    /**
     * Check if user has elevated role (General or Super Admin)
     */
    protected function hasElevatedRole(User $user): bool
    {
        return $user->hasRole(UserRole::GENERAL) || $user->isSuperAdmin();
    }
}
