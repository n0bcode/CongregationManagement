<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CacheManagerInterface;
use App\Enums\UserRole;
use App\Exceptions\CacheException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheManager implements CacheManagerInterface
{
    private const CACHE_PREFIX = 'user_permissions_';
    private const CACHE_TTL = 3600; // 1 hour in seconds
    private const STATS_KEY = 'rbac_cache_stats';

    /**
     * Get cached user permissions
     *
     * Returns null on cache miss or error (graceful degradation)
     */
    public function getUserPermissions(int $userId): ?array
    {
        try {
            $cached = Cache::get($this->getCacheKey($userId));

            if ($cached !== null) {
                $this->incrementCacheMetric('hits');
                Log::debug('Cache hit for user permissions', ['user_id' => $userId]);
            } else {
                $this->incrementCacheMetric('misses');
                Log::debug('Cache miss for user permissions', ['user_id' => $userId]);
            }

            return $cached;
        } catch (\Throwable $e) {
            // Graceful degradation - return null to trigger database fallback
            Log::warning('Cache read failed, falling back to database', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Track cache errors for monitoring
            $this->incrementCacheMetric('errors');

            return null;
        }
    }

    /**
     * Cache user permissions
     *
     * Fails silently - cache writes are not critical
     */
    public function cacheUserPermissions(int $userId, array $permissions): void
    {
        try {
            Cache::put(
                $this->getCacheKey($userId),
                $permissions,
                self::CACHE_TTL
            );

            $this->incrementCacheMetric('writes');

            Log::debug('Cached user permissions', [
                'user_id' => $userId,
                'permission_count' => count($permissions),
                'ttl' => self::CACHE_TTL,
            ]);
        } catch (\Throwable $e) {
            // Fail silently - cache write failures should not block operations
            Log::warning('Cache write failed, continuing without cache', [
                'user_id' => $userId,
                'permission_count' => count($permissions),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Track cache errors for monitoring
            $this->incrementCacheMetric('errors');
        }
    }

    /**
     * Invalidate user permission cache
     *
     * Fails silently - cache invalidation failures are logged but not critical
     */
    public function invalidateUserCache(int $userId): void
    {
        try {
            Cache::forget($this->getCacheKey($userId));

            $this->incrementCacheMetric('invalidations');

            Log::info('Invalidated user permission cache', ['user_id' => $userId]);
        } catch (\Throwable $e) {
            // Fail silently but log for monitoring
            Log::warning('Cache invalidation failed, stale cache may persist', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Track cache errors for monitoring
            $this->incrementCacheMetric('errors');
        }
    }

    /**
     * Invalidate all users with a specific role
     *
     * Continues on individual failures to invalidate as many caches as possible
     */
    public function invalidateRoleCache(UserRole $role): void
    {
        try {
            // Get all users with this role
            $userIds = User::where('role', $role->value)->pluck('id');

            $successCount = 0;
            $failureCount = 0;

            foreach ($userIds as $userId) {
                try {
                    $this->invalidateUserCache($userId);
                    $successCount++;
                } catch (\Throwable $userError) {
                    $failureCount++;
                    Log::warning('Failed to invalidate cache for individual user', [
                        'user_id' => $userId,
                        'role' => $role->value,
                        'error' => $userError->getMessage(),
                    ]);
                }
            }

            Log::info('Invalidated role permission cache', [
                'role' => $role->value,
                'total_users' => count($userIds),
                'success_count' => $successCount,
                'failure_count' => $failureCount,
            ]);

            if ($failureCount > 0) {
                Log::warning('Some cache invalidations failed for role', [
                    'role' => $role->value,
                    'failure_count' => $failureCount,
                ]);
            }
        } catch (\Throwable $e) {
            // Critical error - couldn't even query users
            Log::error('Role cache invalidation failed completely', [
                'role' => $role->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Track cache errors for monitoring
            $this->incrementCacheMetric('errors');
        }
    }

    /**
     * Get cache statistics
     *
     * Returns default values on error
     */
    public function getCacheStats(): array
    {
        try {
            $stats = Cache::get(self::STATS_KEY, [
                'hits' => 0,
                'misses' => 0,
                'writes' => 0,
                'invalidations' => 0,
                'errors' => 0,
            ]);

            // Calculate hit rate if we have data
            $total = $stats['hits'] + $stats['misses'];
            $stats['hit_rate'] = $total > 0 ? round(($stats['hits'] / $total) * 100, 2) : 0;

            return $stats;
        } catch (\Throwable $e) {
            Log::warning('Failed to retrieve cache stats', [
                'error' => $e->getMessage(),
            ]);

            return [
                'hits' => 0,
                'misses' => 0,
                'writes' => 0,
                'invalidations' => 0,
                'errors' => 0,
                'hit_rate' => 0,
                'error' => 'Failed to retrieve statistics',
            ];
        }
    }

    /**
     * Get cache key for a user
     */
    private function getCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }

    /**
     * Increment a cache metric
     *
     * Fails silently - metrics are not critical
     */
    private function incrementCacheMetric(string $metric): void
    {
        try {
            $stats = Cache::get(self::STATS_KEY, [
                'hits' => 0,
                'misses' => 0,
                'writes' => 0,
                'invalidations' => 0,
                'errors' => 0,
            ]);

            $stats[$metric] = ($stats[$metric] ?? 0) + 1;

            Cache::put(self::STATS_KEY, $stats, 86400); // Store stats for 24 hours
        } catch (\Throwable $e) {
            // Silently fail - stats are not critical
            Log::debug('Failed to increment cache metric', [
                'metric' => $metric,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
