<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\UserRole;
use Illuminate\Support\Collection;

interface AuditLoggerInterface
{
    /**
     * Log permission change
     *
     * @param  int  $userId  User who made the change
     * @param  UserRole  $role  Role that was modified
     * @param  array  $permissions  Array of permission keys
     */
    public function logPermissionChange(
        int $userId,
        UserRole $role,
        array $permissions
    ): void;

    /**
     * Log role change
     *
     * @param  int  $userId  User who made the change
     * @param  int  $targetUserId  User whose role was changed
     * @param  UserRole  $oldRole  Previous role
     * @param  UserRole  $newRole  New role
     */
    public function logRoleChange(
        int $userId,
        int $targetUserId,
        UserRole $oldRole,
        UserRole $newRole
    ): void;

    /**
     * Get audit history for a role
     */
    public function getRoleAuditHistory(UserRole $role): Collection;

    /**
     * Get audit history for a user
     */
    public function getUserAuditHistory(int $userId): Collection;
}
