# Design Document

## Overview

This design document specifies the architecture and implementation approach for completing the RBAC (Role-Based Access Control) system in the Managing the Congregation application. Building upon the foundation established in Story 1.4, this design focuses on production-ready features including caching optimization, permission management UI, auto-discovery, comprehensive audit logging, and full integration with all application modules.

The design follows Laravel best practices and maintains the type-safe, performant approach established in the initial implementation while adding enterprise-grade features for security, monitoring, and maintainability.

## Architecture

### System Components

```
┌────────────────────────────────────────────────────────────────┐
│                     RBAC System Architecture                    │
├────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────┐                                           │
│  │  Web Interface  │                                           │
│  │  (Blade Views)  │                                           │
│  └────────┬────────┘                                           │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────────────────────────────────────────┐       │
│  │         Permission Management Controller            │       │
│  │  - index()  - update()  - sync()  - audit()        │       │
│  └────────┬────────────────────────────────────────────┘       │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────────────────────────────────────────┐       │
│  │            Permission Service Layer                 │       │
│  │  - assignPermissionsToRole()                        │       │
│  │  - getRolePermissions()                             │       │
│  │  - syncPermissionsFromRoutes()                      │       │
│  │  - invalidateCache()                                │       │
│  └────────┬────────────────────────────────────────────┘       │
│           │                                                     │
│           ├──────────────┬──────────────┬──────────────┐       │
│           ▼              ▼              ▼              ▼       │
│  ┌──────────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐  │
│  │   Database   │ │  Cache   │ │  Audit   │ │   Routes     │  │
│  │              │ │  Layer   │ │  Logger  │ │  Scanner     │  │
│  │ - users      │ │          │ │          │ │              │  │
│  │ - permissions│ │  Redis/  │ │  audit_  │ │  Route::     │  │
│  │ - role_perms │ │  File    │ │  logs    │ │  getRoutes() │  │
│  └──────────────┘ └──────────┘ └──────────┘ └──────────────┘  │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Authorization Layer                        │   │
│  │  - Policies (MemberPolicy, FinancialPolicy, etc.)      │   │
│  │  - Middleware (CheckPermission)                         │   │
│  │  - Gates (view-admin, manage-permissions)              │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└────────────────────────────────────────────────────────────────┘
```

### Data Flow

**Permission Check Flow:**

```
User Request
    ↓
Middleware (auth, permission:members.view)
    ↓
Check Cache (user_permissions_{user_id})
    ├─ Hit → Return cached result
    └─ Miss → Query Database
        ↓
    Check role_permissions table
        ↓
    Cache result (1 hour TTL)
        ↓
    Return authorized/forbidden
```

**Permission Update Flow:**

```
Admin Updates Permissions
    ↓
PermissionManagementController::update()
    ↓
PermissionService::assignPermissionsToRole()
    ↓
Database Transaction
    ├─ Delete old role_permissions
    ├─ Insert new role_permissions
    └─ Commit
        ↓
    Invalidate affected caches
        ↓
    Log audit trail
        ↓
    Return success response
```

## Components and Interfaces

### 1. Permission Service

**Interface:**

```php
interface PermissionServiceInterface
{
    /**
     * Assign permissions to a role
     */
    public function assignPermissionsToRole(
        UserRole $role,
        array $permissionKeys
    ): void;

    /**
     * Get all permissions for a role
     */
    public function getRolePermissions(UserRole $role): Collection;

    /**
     * Sync permissions from route definitions
     */
    public function syncPermissionsFromRoutes(): array;

    /**
     * Invalidate permission cache for a role
     */
    public function invalidateRoleCache(UserRole $role): void;

    /**
     * Invalidate permission cache for a user
     */
    public function invalidateUserCache(int $userId): void;

    /**
     * Get permission usage statistics
     */
    public function getPermissionStats(): array;
}
```

**Implementation:**

```php
class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private CacheManager $cache,
        private AuditLogger $auditLogger
    ) {}

    public function assignPermissionsToRole(
        UserRole $role,
        array $permissionKeys
    ): void {
        DB::transaction(function () use ($role, $permissionKeys) {
            // Delete existing permissions
            DB::table('role_permissions')
                ->where('role', $role->value)
                ->delete();

            // Validate and insert new permissions
            $validPermissions = Permission::whereIn('key', $permissionKeys)
                ->pluck('id');

            $inserts = $validPermissions->map(fn($id) => [
                'role' => $role->value,
                'permission_id' => $id
            ]);

            DB::table('role_permissions')->insert($inserts->toArray());

            // Invalidate cache
            $this->invalidateRoleCache($role);

            // Log audit
            $this->auditLogger->logPermissionChange(
                Auth::id(),
                $role,
                $permissionKeys
            );
        });
    }
}
```

### 2. Permission Management Controller

**Routes:**

```php
Route::middleware(['auth', 'can:view-admin'])->group(function () {
    Route::get('/admin/permissions', [PermissionManagementController::class, 'index'])
        ->name('admin.permissions.index');

    Route::post('/admin/permissions/update', [PermissionManagementController::class, 'update'])
        ->name('admin.permissions.update');

    Route::post('/admin/permissions/sync', [PermissionManagementController::class, 'sync'])
        ->name('admin.permissions.sync');

    Route::get('/admin/permissions/audit', [PermissionManagementController::class, 'audit'])
        ->name('admin.permissions.audit');
});
```

**Controller Methods:**

```php
class PermissionManagementController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index()
    {
        $roles = UserRole::cases();
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $this->buildPermissionMatrix();

        return view('admin.permissions.index', compact(
            'roles',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,key']
        ]);

        $role = UserRole::from($validated['role']);

        $this->permissionService->assignPermissionsToRole(
            $role,
            $validated['permissions']
        );

        return back()->with('success', 'Permissions updated successfully');
    }

    public function sync()
    {
        $results = $this->permissionService->syncPermissionsFromRoutes();

        return back()->with('success', sprintf(
            'Synced %d permissions (%d new, %d updated, %d orphaned)',
            $results['total'],
            $results['new'],
            $results['updated'],
            $results['orphaned']
        ));
    }

    public function audit()
    {
        $logs = AuditLog::where('action', 'permission_updated')
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('admin.permissions.audit', compact('logs'));
    }
}
```

### 3. Cache Manager

**Interface:**

```php
interface CacheManagerInterface
{
    /**
     * Get cached user permissions
     */
    public function getUserPermissions(int $userId): ?array;

    /**
     * Cache user permissions
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
     */
    public function getCacheStats(): array;
}
```

**Implementation:**

```php
class CacheManager implements CacheManagerInterface
{
    private const CACHE_PREFIX = 'user_permissions_';
    private const CACHE_TTL = 3600; // 1 hour

    public function getUserPermissions(int $userId): ?array
    {
        return Cache::get($this->getCacheKey($userId));
    }

    public function cacheUserPermissions(int $userId, array $permissions): void
    {
        Cache::put(
            $this->getCacheKey($userId),
            $permissions,
            self::CACHE_TTL
        );

        // Track cache writes for monitoring
        $this->incrementCacheMetric('writes');
    }

    public function invalidateUserCache(int $userId): void
    {
        Cache::forget($this->getCacheKey($userId));
        $this->incrementCacheMetric('invalidations');
    }

    public function invalidateRoleCache(UserRole $role): void
    {
        // Get all users with this role
        $userIds = User::where('role', $role->value)->pluck('id');

        foreach ($userIds as $userId) {
            $this->invalidateUserCache($userId);
        }
    }

    private function getCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }
}
```

### 4. Audit Logger

**Interface:**

```php
interface AuditLoggerInterface
{
    /**
     * Log permission change
     */
    public function logPermissionChange(
        int $userId,
        UserRole $role,
        array $permissions
    ): void;

    /**
     * Log role change
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
```

**Implementation:**

```php
class AuditLogger implements AuditLoggerInterface
{
    public function logPermissionChange(
        int $userId,
        UserRole $role,
        array $permissions
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'permission_updated',
            'target_type' => 'role',
            'target_id' => $role->value,
            'changes' => [
                'permissions' => $permissions
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function logRoleChange(
        int $userId,
        int $targetUserId,
        UserRole $oldRole,
        UserRole $newRole
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'role_changed',
            'target_type' => 'user',
            'target_id' => $targetUserId,
            'changes' => [
                'old_role' => $oldRole->value,
                'new_role' => $newRole->value
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

### 5. Route Scanner

**Interface:**

```php
interface RouteScannerInterface
{
    /**
     * Scan all routes and extract permission requirements
     */
    public function scanRoutes(): Collection;

    /**
     * Extract permission from middleware
     */
    public function extractPermissionFromMiddleware(array $middleware): ?string;

    /**
     * Generate permission metadata from route
     */
    public function generatePermissionMetadata(Route $route): array;
}
```

**Implementation:**

```php
class RouteScanner implements RouteScannerInterface
{
    public function scanRoutes(): Collection
    {
        $routes = Route::getRoutes();
        $permissions = collect();

        foreach ($routes as $route) {
            $permission = $this->extractPermissionFromMiddleware(
                $route->middleware()
            );

            if ($permission) {
                $permissions->push([
                    'key' => $permission,
                    'name' => $this->generateName($permission),
                    'module' => $this->extractModule($permission),
                    'route_name' => $route->getName(),
                    'route_uri' => $route->uri(),
                    'methods' => $route->methods()
                ]);
            }
        }

        return $permissions->unique('key');
    }

    public function extractPermissionFromMiddleware(array $middleware): ?string
    {
        foreach ($middleware as $m) {
            if (str_starts_with($m, 'permission:')) {
                return str_replace('permission:', '', $m);
            }
        }

        return null;
    }

    private function generateName(string $key): string
    {
        // Convert 'members.view' to 'View Members'
        [$module, $action] = explode('.', $key);

        return ucfirst($action) . ' ' . ucfirst($module);
    }

    private function extractModule(string $key): string
    {
        return explode('.', $key)[0];
    }
}
```

## Data Models

### Permission Model

```php
class Permission extends Model
{
    protected $fillable = ['key', 'name', 'module', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'role_permissions',
            'permission_id',
            'role'
        );
    }

    /**
     * Scope to active permissions only
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to a specific module
     */
    public function scopeModule(Builder $query, string $module): void
    {
        $query->where('module', $module);
    }
}
```

### AuditLog Model

```php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to permission-related actions
     */
    public function scopePermissionActions(Builder $query): void
    {
        $query->whereIn('action', [
            'permission_updated',
            'role_changed'
        ]);
    }

    /**
     * Scope to a specific target
     */
    public function scopeTarget(Builder $query, string $type, $id): void
    {
        $query->where('target_type', $type)
              ->where('target_id', $id);
    }
}
```

### Updated User Model

```php
class User extends Authenticatable
{
    protected $casts = [
        'role' => UserRole::class
    ];

    /**
     * Check if user has a specific permission (with caching)
     */
    public function hasPermission(PermissionKey|string $permission): bool
    {
        // Super admin bypass
        if ($this->role === UserRole::SUPER_ADMIN) {
            return true;
        }

        $key = $permission instanceof PermissionKey
            ? $permission->value
            : $permission;

        // Get cached permissions
        $cacheManager = app(CacheManager::class);
        $permissions = $cacheManager->getUserPermissions($this->id);

        if ($permissions === null) {
            // Cache miss - query database
            $permissions = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', $this->role->value)
                ->where('permissions.is_active', true)
                ->pluck('permissions.key')
                ->toArray();

            // Cache the result
            $cacheManager->cacheUserPermissions($this->id, $permissions);
        }

        return in_array($key, $permissions);
    }

    /**
     * Boot method to handle cache invalidation
     */
    protected static function booted()
    {
        static::updated(function (User $user) {
            if ($user->isDirty('role')) {
                $cacheManager = app(CacheManager::class);
                $cacheManager->invalidateUserCache($user->id);

                // Log role change
                $auditLogger = app(AuditLogger::class);
                $auditLogger->logRoleChange(
                    Auth::id(),
                    $user->id,
                    UserRole::from($user->getOriginal('role')),
                    $user->role
                );
            }
        });
    }
}
```

## Correctness Properties

_A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees._

### Property Reflection

Before defining properties, let's identify and eliminate redundancy:

**Redundancy Analysis:**

- Properties 2.1, 2.2, 2.3 (caching) can be combined into comprehensive caching properties
- Properties 3.1, 3.2 (Director scoping) are essentially the same rule applied to different models - can be generalized
- Properties 8.1, 8.2, 8.3 (audit logging) follow the same pattern - can be combined
- Properties 11.1, 11.2, 11.3, 11.4 (policy integration) are the same pattern - can be generalized

**Consolidated Properties:**
After reflection, we'll focus on unique validation properties that provide distinct correctness guarantees.

### Correctness Properties

**Property 1: Permission cache consistency**
_For any_ user, when their permissions are checked twice within the cache TTL period, the second check should use cached data without querying the database
**Validates: Requirements 2.1**

**Property 2: Cache invalidation on role change**
_For any_ user, when their role is changed, subsequent permission checks should reflect the new role's permissions immediately (cache must be invalidated)
**Validates: Requirements 2.2**

**Property 3: Bulk cache invalidation on role permission update**
_For any_ role, when its permissions are updated, all users with that role should have their permission caches invalidated
**Validates: Requirements 2.3**

**Property 4: Community scoping for Directors**
_For any_ Director user and any model with community_id, queries should automatically return only records matching the Director's community_id
**Validates: Requirements 3.1, 3.2**

**Property 5: No community scoping for elevated roles**
_For any_ user with role SUPER_ADMIN or GENERAL, queries should return records from all communities without filtering
**Validates: Requirements 3.3**

**Property 6: Super admin universal access**
_For any_ action and any resource, a user with role SUPER_ADMIN should always be authorized without permission checks
**Validates: Requirements 4.2**

**Property 7: Permission-based authorization**
_For any_ non-super-admin user and any action, authorization should succeed if and only if the user's role has the required permission
**Validates: Requirements 4.3**

**Property 8: Permission assignment idempotence**
_For any_ role and set of permissions, assigning the same permissions multiple times should result in the same final state
**Validates: Requirements 5.2**

**Property 9: Permission revocation completeness**
_For any_ role, after revoking all permissions, the role should have zero permissions assigned
**Validates: Requirements 5.3**

**Property 10: Invalid permission rejection**
_For any_ role and any non-existent permission key, attempting to assign that permission should fail with validation error
**Validates: Requirements 5.5**

**Property 11: Permission update atomicity**
_For any_ role permission update, either all changes are applied successfully or none are applied (database transaction)
**Validates: Requirements 6.3**

**Property 12: Audit trail completeness**
_For any_ permission change operation (assign, revoke, role change), an audit log entry should be created with timestamp and user ID
**Validates: Requirements 6.5, 8.1, 8.2, 8.3**

**Property 13: Route permission discovery**
_For any_ route with permission middleware, running the sync command should create or update the corresponding permission record
**Validates: Requirements 7.1, 7.2**

**Property 14: Soft delete for removed permissions**
_For any_ permission that exists in database but not in routes, the sync command should mark it as inactive rather than deleting it
**Validates: Requirements 7.3**

**Property 15: Authorization failure response**
_For any_ user without required permissions, attempting to access a protected resource should return HTTP 403 Forbidden
**Validates: Requirements 9.2**

**Property 16: Unauthenticated redirect**
_For any_ unauthenticated request to a protected route, the system should redirect to the login page
**Validates: Requirements 9.3**

**Property 17: Policy integration consistency**
_For any_ policy method (Member, Financial, Document, Community), authorization should use the RBAC permission system
**Validates: Requirements 11.1, 11.2, 11.3, 11.4**

## Error Handling

### Error Categories

**1. Permission Not Found Errors**

```php
class PermissionNotFoundException extends Exception
{
    public function __construct(string $permissionKey)
    {
        parent::__construct("Permission '{$permissionKey}' not found in system");
    }
}
```

**Handling Strategy:**

- Thrown when attempting to assign non-existent permissions
- Caught in PermissionService and logged
- User-friendly error message returned to admin
- System continues operation (non-fatal)

**2. Cache Errors**

```php
class CacheException extends Exception
{
    public function __construct(string $operation, Throwable $previous)
    {
        parent::__construct("Cache operation '{$operation}' failed", 0, $previous);
    }
}
```

**Handling Strategy:**

- Caught and logged but not propagated
- System falls back to database queries
- Cache errors should never block authorization
- Alert monitoring system for cache infrastructure issues

**3. Authorization Errors**

```php
class UnauthorizedException extends Exception
{
    public function __construct(string $action, string $resource)
    {
        parent::__construct("Unauthorized to perform '{$action}' on '{$resource}'");
    }
}
```

**Handling Strategy:**

- Converted to HTTP 403 Forbidden response
- Logged with user ID and attempted action
- Clear error message for debugging
- No sensitive information exposed to user

**4. Database Transaction Errors**

```php
class PermissionUpdateException extends Exception
{
    public function __construct(UserRole $role, Throwable $previous)
    {
        parent::__construct(
            "Failed to update permissions for role '{$role->value}'",
            0,
            $previous
        );
    }
}
```

**Handling Strategy:**

- Transaction automatically rolled back
- Error logged with full stack trace
- User notified of failure
- System state remains consistent

### Error Recovery Patterns

**Pattern 1: Graceful Cache Degradation**

```php
public function getUserPermissions(int $userId): array
{
    try {
        $cached = $this->cacheManager->getUserPermissions($userId);
        if ($cached !== null) {
            return $cached;
        }
    } catch (CacheException $e) {
        Log::warning('Cache read failed, falling back to database', [
            'user_id' => $userId,
            'error' => $e->getMessage()
        ]);
    }

    // Fallback to database
    return $this->queryPermissionsFromDatabase($userId);
}
```

**Pattern 2: Transaction Rollback with Logging**

```php
public function assignPermissionsToRole(UserRole $role, array $permissions): void
{
    try {
        DB::transaction(function () use ($role, $permissions) {
            // Delete old permissions
            // Insert new permissions
            // Invalidate cache
            // Log audit
        });
    } catch (Throwable $e) {
        Log::error('Permission assignment failed', [
            'role' => $role->value,
            'permissions' => $permissions,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw new PermissionUpdateException($role, $e);
    }
}
```

**Pattern 3: Validation Before Operation**

```php
public function assignPermissionsToRole(UserRole $role, array $permissions): void
{
    // Validate all permissions exist
    $validPermissions = Permission::whereIn('key', $permissions)
        ->where('is_active', true)
        ->pluck('key')
        ->toArray();

    $invalidPermissions = array_diff($permissions, $validPermissions);

    if (!empty($invalidPermissions)) {
        throw new PermissionNotFoundException(
            implode(', ', $invalidPermissions)
        );
    }

    // Proceed with assignment
    // ...
}
```

### Logging Strategy

**Log Levels:**

- **DEBUG**: Cache hits/misses, permission checks
- **INFO**: Permission assignments, role changes, sync operations
- **WARNING**: Cache failures, invalid permission attempts
- **ERROR**: Transaction failures, database errors
- **CRITICAL**: System-wide authorization failures

**Log Format:**

```php
Log::info('Permission assigned', [
    'admin_user_id' => Auth::id(),
    'target_role' => $role->value,
    'permissions' => $permissions,
    'timestamp' => now()->toIso8601String(),
    'ip_address' => request()->ip()
]);
```

## Testing Strategy

### Unit Testing Approach

**Focus Areas:**

1. Permission checking logic (User::hasPermission)
2. Cache manager operations
3. Permission service methods
4. Audit logger functionality
5. Route scanner extraction logic

**Example Unit Test:**

```php
test('user has permission returns true for assigned permissions', function () {
    $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

    // Assign permission
    $permission = Permission::factory()->create(['key' => 'members.view']);
    DB::table('role_permissions')->insert([
        'role' => UserRole::DIRECTOR->value,
        'permission_id' => $permission->id
    ]);

    expect($user->hasPermission(PermissionKey::MEMBERS_VIEW))->toBeTrue();
    expect($user->hasPermission('members.view'))->toBeTrue();
    expect($user->hasPermission('members.delete'))->toBeFalse();
});
```

### Property-Based Testing Approach

**Property Test 1: Cache Consistency**

```php
test('permission checks use cache on subsequent calls', function () {
    // Generate random user with random role
    $user = User::factory()->create([
        'role' => fake()->randomElement(UserRole::cases())
    ]);

    // First check (cache miss)
    DB::enableQueryLog();
    $result1 = $user->hasPermission('members.view');
    $queryCount1 = count(DB::getQueryLog());
    DB::disableQueryLog();

    // Second check (cache hit)
    DB::enableQueryLog();
    $result2 = $user->hasPermission('members.view');
    $queryCount2 = count(DB::getQueryLog());
    DB::disableQueryLog();

    // Results should be identical
    expect($result1)->toBe($result2);
    // Second call should have fewer queries (cache hit)
    expect($queryCount2)->toBeLessThan($queryCount1);
});
```

**Property Test 2: Community Scoping**

```php
test('directors only see their community data', function () {
    // Generate random communities
    $communities = Community::factory()->count(3)->create();

    // Generate random director for first community
    $director = User::factory()->create([
        'role' => UserRole::DIRECTOR,
        'community_id' => $communities[0]->id
    ]);

    // Generate random members across all communities
    foreach ($communities as $community) {
        Member::factory()->count(5)->create([
            'community_id' => $community->id
        ]);
    }

    // Act as director and query members
    Auth::login($director);
    $visibleMembers = Member::all();

    // All visible members should belong to director's community
    expect($visibleMembers->every(fn($m) => $m->community_id === $director->community_id))
        ->toBeTrue();
});
```

**Property Test 3: Super Admin Bypass**

```php
test('super admin can perform any action', function () {
    $superAdmin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

    // Generate random permission keys
    $randomPermissions = fake()->randomElements(
        array_map(fn($case) => $case->value, PermissionKey::cases()),
        5
    );

    // Super admin should have all permissions without assignment
    foreach ($randomPermissions as $permission) {
        expect($superAdmin->hasPermission($permission))->toBeTrue();
    }
});
```

**Property Test 4: Permission Assignment Idempotence**

```php
test('assigning same permissions multiple times produces same result', function () {
    $role = fake()->randomElement(UserRole::cases());
    $permissions = fake()->randomElements(
        Permission::pluck('key')->toArray(),
        3
    );

    $service = app(PermissionService::class);

    // Assign permissions first time
    $service->assignPermissionsToRole($role, $permissions);
    $result1 = $service->getRolePermissions($role)->pluck('key')->sort()->values();

    // Assign same permissions again
    $service->assignPermissionsToRole($role, $permissions);
    $result2 = $service->getRolePermissions($role)->pluck('key')->sort()->values();

    // Results should be identical
    expect($result1->toArray())->toBe($result2->toArray());
});
```

**Property Test 5: Audit Trail Completeness**

```php
test('all permission changes create audit logs', function () {
    $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
    Auth::login($admin);

    $role = fake()->randomElement([UserRole::DIRECTOR, UserRole::GENERAL]);
    $permissions = fake()->randomElements(
        Permission::pluck('key')->toArray(),
        3
    );

    $service = app(PermissionService::class);

    // Record initial audit log count
    $initialCount = AuditLog::count();

    // Perform permission change
    $service->assignPermissionsToRole($role, $permissions);

    // Verify audit log was created
    expect(AuditLog::count())->toBe($initialCount + 1);

    $log = AuditLog::latest()->first();
    expect($log->user_id)->toBe($admin->id);
    expect($log->action)->toBe('permission_updated');
    expect($log->target_id)->toBe($role->value);
});
```

### Integration Testing Approach

**Test Scenarios:**

1. Complete permission management workflow (assign → check → revoke)
2. Cache invalidation across role updates
3. Policy integration with RBAC system
4. Route permission sync command
5. Audit log retrieval and filtering

**Example Integration Test:**

```php
test('complete permission management workflow', function () {
    $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
    $this->actingAs($admin);

    // 1. View permission management page
    $response = $this->get(route('admin.permissions.index'));
    $response->assertOk();
    $response->assertViewHas('roles');
    $response->assertViewHas('permissions');

    // 2. Update role permissions
    $response = $this->post(route('admin.permissions.update'), [
        'role' => UserRole::DIRECTOR->value,
        'permissions' => ['members.view', 'members.create']
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 3. Verify permissions were assigned
    $service = app(PermissionService::class);
    $permissions = $service->getRolePermissions(UserRole::DIRECTOR);
    expect($permissions->pluck('key')->toArray())
        ->toContain('members.view', 'members.create');

    // 4. Verify audit log was created
    $log = AuditLog::latest()->first();
    expect($log->action)->toBe('permission_updated');
    expect($log->user_id)->toBe($admin->id);
});
```

### Performance Testing

**Benchmarks:**

- Permission check (cached): < 1ms
- Permission check (uncached): < 10ms
- Permission assignment: < 50ms
- Route sync command: < 5 seconds for 100 routes
- Cache invalidation: < 100ms for 50 users

**Performance Test Example:**

```php
test('permission checks are fast with caching', function () {
    $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

    // Warm up cache
    $user->hasPermission('members.view');

    // Measure cached performance
    $start = microtime(true);
    for ($i = 0; $i < 100; $i++) {
        $user->hasPermission('members.view');
    }
    $duration = (microtime(true) - $start) * 1000; // Convert to ms

    // Average should be < 1ms per check
    expect($duration / 100)->toBeLessThan(1.0);
});
```

### Test Coverage Goals

- **Unit Tests**: ≥95% coverage for service layer
- **Integration Tests**: All critical workflows covered
- **Property Tests**: All correctness properties implemented
- **Performance Tests**: All benchmarks validated
- **Edge Cases**: Invalid inputs, concurrent updates, cache failures

## Implementation Notes

### Migration Strategy

**Phase 1: Extend Permission Keys (Week 1)**

- Add all module permissions to PermissionKey enum
- Run permission seeder to populate database
- Update default role assignments

**Phase 2: Implement Caching (Week 1-2)**

- Create CacheManager service
- Update User::hasPermission to use cache
- Implement cache invalidation on role changes
- Add cache monitoring

**Phase 3: Build Management UI (Week 2-3)**

- Create PermissionManagementController
- Build permission matrix view
- Implement AJAX permission updates
- Add audit log viewer

**Phase 4: Auto-Discovery (Week 3-4)**

- Create RouteScanner service
- Build sync command
- Implement orphaned permission detection
- Add sync to deployment process

**Phase 5: Audit & Integration (Week 4)**

- Complete AuditLogger implementation
- Integrate with all policies
- Add comprehensive logging
- Performance optimization

### Deployment Checklist

- [ ] Run migrations for audit_logs table
- [ ] Seed new permissions for all modules
- [ ] Configure cache driver (Redis recommended)
- [ ] Run permission sync command
- [ ] Verify all policies use RBAC system
- [ ] Test cache invalidation
- [ ] Review audit logs
- [ ] Performance benchmarks pass
- [ ] Documentation updated
- [ ] Training materials prepared

### Monitoring & Maintenance

**Metrics to Track:**

- Cache hit rate (target: >90%)
- Average permission check time (target: <10ms)
- Failed authorization attempts per day
- Permission changes per week
- Orphaned permissions count

**Regular Tasks:**

- Weekly: Review audit logs for suspicious activity
- Monthly: Run permission sync and review orphaned permissions
- Quarterly: Performance audit and optimization
- Annually: Full RBAC system review and permission cleanup

### Security Considerations

**Best Practices:**

1. Always use type-safe enums, never string literals
2. Log all permission changes with user context
3. Implement rate limiting on permission management endpoints
4. Regular audit log reviews
5. Principle of least privilege for default role assignments
6. Secure cache storage (encrypted Redis)
7. Monitor for privilege escalation attempts

**Threat Mitigation:**

- **Privilege Escalation**: Super admin bypass only in Policy before() method
- **Cache Poisoning**: Cache keys include user ID, invalidation on role change
- **Audit Log Tampering**: Immutable logs, separate from main database
- **Permission Bypass**: All routes protected by middleware or policy
- **Brute Force**: Rate limiting on auth and permission endpoints
