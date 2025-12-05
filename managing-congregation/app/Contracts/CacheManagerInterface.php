<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\UserRole;

interface CacheManagerInterface
{
    /**
     * Get cached user permissions
     *
     * @return array|null Array of permission keys or null if not cached
     */
    public function getUserPermissions(int $userId): ?array;

    /**
     * Cache user permissions
     *
     * @param  array  $permissions  Array of permission keys
     */
    public function cacheUserPermissions(int $userId, array $permissions): void;

    /**
     * Invalidate user permission cache
     */
    public function invalidateUserCache(int $userId): void;

    /**
     * Invalidate all users with a specific role
     */
    public function invalidateRoleCache(UserRole $role): void;

    /**
     * Get cache statistics
     *
     * @return array{hits: int, misses: int, writes: int, invalidations: int}
     */
    public function getCacheStats(): array;
}
