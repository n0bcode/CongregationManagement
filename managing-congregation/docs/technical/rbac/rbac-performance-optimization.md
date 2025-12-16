# RBAC System Performance Optimization

## Overview

This document outlines the performance optimizations implemented for the RBAC (Role-Based Access Control) system and provides recommendations for production deployment.

## Database Optimizations

### Indexes

The following indexes have been added to optimize permission queries:

#### Permissions Table

-   **`key` (unique)**: Fast lookup by permission key
-   **`module`**: Fast filtering by module
-   **`is_active`**: Fast filtering of active permissions
-   **`[module, is_active]` (composite)**: Optimized for common query pattern

#### Role Permissions Table

-   **`[role, permission_id]` (primary key)**: Fast lookup and prevents duplicates
-   **`permission_id` (foreign key)**: Automatic index for joins

### Query Optimization

1. **Join-based queries**: Using database joins instead of Eloquent relationships to avoid N+1 queries
2. **Eager loading**: Using `->with('user')` for audit logs to prevent N+1 queries
3. **Selective columns**: Using `select()` to fetch only needed columns

## Caching Strategy

### Cache Implementation

The RBAC system uses a multi-layer caching strategy:

1. **User Permission Cache**

    - Cache key: `user_permissions_{user_id}`
    - TTL: 1 hour (3600 seconds)
    - Stores: Array of permission keys for a user

2. **Cache Invalidation**
    - Automatic invalidation when user role changes
    - Bulk invalidation when role permissions are updated
    - Graceful fallback to database on cache failures

### Cache Drivers

#### Development

-   **Database cache** (default): Simple, no additional setup required
-   Good for development and testing

#### Production (Recommended)

-   **Redis cache**: High-performance, distributed caching
-   Configuration:
    ```env
    CACHE_STORE=redis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=your_secure_password
    REDIS_PORT=6379
    ```

### Cache Performance Metrics

Target performance benchmarks:

-   **Permission check (cached)**: < 1ms
-   **Permission check (uncached)**: < 10ms
-   **Cache invalidation**: < 100ms for 50 users

## Performance Best Practices

### 1. Use Super Admin Bypass

Super admins skip permission checks entirely:

```php
// In Policy before() method
public function before(User $user): ?bool
{
    if ($user->role === UserRole::SUPER_ADMIN) {
        return true; // Skip all permission checks
    }

    return null;
}
```

### 2. Cache Permission Checks

Always use the cached `hasPermission()` method:

```php
// Good - uses cache
if ($user->hasPermission('members.view')) {
    // ...
}

// Bad - direct database query
if (DB::table('role_permissions')->where(...)->exists()) {
    // ...
}
```

### 3. Batch Permission Assignments

Assign multiple permissions in a single transaction:

```php
// Good - single transaction
$permissionService->assignPermissionsToRole($role, [
    'members.view',
    'members.create',
    'members.edit',
]);

// Bad - multiple transactions
foreach ($permissions as $permission) {
    $permissionService->assignPermissionsToRole($role, [$permission]);
}
```

### 4. Use Middleware for Route Protection

Middleware checks permissions before controller execution:

```php
Route::middleware(['auth', 'permission:members.view'])
    ->get('/members', [MemberController::class, 'index']);
```

## Monitoring

### Key Metrics to Track

1. **Cache Hit Rate**

    - Target: > 90%
    - Monitor: `CacheManager::getCacheStats()`

2. **Permission Check Latency**

    - Target: < 10ms (uncached), < 1ms (cached)
    - Monitor: Application logs

3. **Failed Authorization Attempts**

    - Monitor: Audit logs
    - Alert on unusual patterns

4. **Orphaned Permissions**
    - Run: `php artisan permissions:sync --dry-run`
    - Review regularly

### Logging

The system logs performance-critical operations:

```php
// Permission checks (DEBUG level)
Log::debug('Permission check', [
    'user_id' => $userId,
    'permission' => $permissionKey,
    'cached' => $wasCached,
    'duration_ms' => $duration,
]);

// Cache operations (INFO level)
Log::info('Cache invalidated', [
    'role' => $role->value,
    'affected_users' => $userCount,
]);
```

## Production Deployment Checklist

-   [ ] Configure Redis cache driver
-   [ ] Run database migrations (indexes)
-   [ ] Run `php artisan permissions:sync`
-   [ ] Verify cache is working: `php artisan cache:clear`
-   [ ] Test permission checks with monitoring
-   [ ] Set up cache monitoring dashboard
-   [ ] Configure log aggregation
-   [ ] Set up alerts for failed authorization attempts
-   [ ] Document cache invalidation strategy for deployments

## Troubleshooting

### Slow Permission Checks

1. Check cache hit rate: `CacheManager::getCacheStats()`
2. Verify Redis is running (if using Redis)
3. Check database indexes: `SHOW INDEX FROM permissions`
4. Review slow query log

### Cache Inconsistencies

1. Clear cache: `php artisan cache:clear`
2. Verify cache invalidation is working
3. Check for race conditions in concurrent updates
4. Review cache TTL settings

### High Memory Usage

1. Monitor cache size
2. Adjust cache TTL if needed
3. Consider cache eviction policies
4. Review permission data size

## Benchmarking

To run performance benchmarks:

```bash
# Run all tests including performance tests
php artisan test

# Run only performance tests
php artisan test --filter=Performance
```

## Future Optimizations

Potential improvements for high-scale deployments:

1. **Permission Preloading**: Load all permissions for a user's role on login
2. **Cache Warming**: Pre-populate cache for active users
3. **Read Replicas**: Use database read replicas for permission queries
4. **CDN Caching**: Cache permission matrix for static roles
5. **Query Result Caching**: Cache complex permission queries

## References

-   Laravel Caching: https://laravel.com/docs/cache
-   Redis Best Practices: https://redis.io/docs/manual/patterns/
-   Database Indexing: https://laravel.com/docs/migrations#indexes
