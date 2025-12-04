# RBAC Error Handling and Logging Documentation

## Overview

This document describes the comprehensive error handling and logging implementation for the RBAC (Role-Based Access Control) system in the Managing the Congregation application.

## Important Note

The `is_active` column for the `permissions` table will be added when implementing tasks 13-14 (Route Scanner and Permission Sync). Until then, all permissions are considered active.

## Custom Exception Classes

### 1. PermissionNotFoundException

**Location:** `app/Exceptions/PermissionNotFoundException.php`

**Purpose:** Thrown when attempting to assign or check a permission that doesn't exist in the system.

**Usage:**

```php
throw new PermissionNotFoundException('members.invalid_action');
```

**Behavior:**

-   Logs warning level message
-   Includes permission key in message
-   Does not block application flow

### 2. CacheException

**Location:** `app/Exceptions/CacheException.php`

**Purpose:** Thrown when cache operations fail (read, write, invalidate).

**Usage:**

```php
throw new CacheException('read', $previousException);
```

**Behavior:**

-   Logs error level message
-   Includes operation type and previous exception
-   System falls back to database queries
-   Never blocks authorization checks

### 3. UnauthorizedException

**Location:** `app/Exceptions/UnauthorizedException.php`

**Purpose:** Thrown when a user attempts an action they're not authorized to perform.

**Usage:**

```php
throw new UnauthorizedException('delete', 'member');
```

**Behavior:**

-   Logs warning level message with user context
-   Returns HTTP 403 Forbidden response
-   Includes IP address and user agent for security monitoring
-   Renders custom 403 error page

### 4. PermissionUpdateException

**Location:** `app/Exceptions/PermissionUpdateException.php`

**Purpose:** Thrown when database transaction fails during permission assignment.

**Usage:**

```php
throw new PermissionUpdateException($role, $previousException);
```

**Behavior:**

-   Logs error level message with full context
-   Includes role and previous exception details
-   Transaction automatically rolled back
-   User notified of failure

### 5. AuditLogException

**Location:** `app/Exceptions/AuditLogException.php`

**Purpose:** Thrown when audit logging fails (critical for security).

**Usage:**

```php
throw new AuditLogException('permission_updated', $previousException);
```

**Behavior:**

-   Logs critical level message
-   Includes action type and previous exception
-   Should trigger alerts in production
-   Does not block operations (logged but not thrown)

## Error Handling Patterns

### Pattern 1: Graceful Cache Degradation

**Implementation:** `CacheManager::getUserPermissions()`

```php
try {
    $cached = Cache::get($this->getCacheKey($userId));
    return $cached;
} catch (\Throwable $e) {
    Log::warning('Cache read failed, falling back to database', [...]);
    return null; // Triggers database fallback
}
```

**Behavior:**

-   Cache failures never block operations
-   System automatically falls back to database
-   Performance may degrade but functionality maintained
-   Errors tracked for monitoring

### Pattern 2: Transaction Rollback with Logging

**Implementation:** `PermissionService::assignPermissionsToRole()`

```php
try {
    DB::transaction(function () use ($role, $permissions) {
        // Delete old permissions
        // Insert new permissions
        // Invalidate cache
        // Log audit
    });
} catch (\Throwable $e) {
    Log::error('Permission assignment failed', [...]);
    throw new PermissionUpdateException($role, $e);
}
```

**Behavior:**

-   All changes rolled back on any failure
-   Database remains consistent
-   Full error context logged
-   User receives clear error message

### Pattern 3: Validation Before Operation

**Implementation:** `PermissionService::assignPermissionsToRole()`

```php
// Validate all permissions exist before starting transaction
$validPermissions = Permission::whereIn('key', $permissionKeys)
    ->where('is_active', true)
    ->get();

$invalidKeys = array_diff($permissionKeys, $validKeys);

if (!empty($invalidKeys)) {
    throw new PermissionNotFoundException(implode(', ', $invalidKeys));
}
```

**Behavior:**

-   Validates input before expensive operations
-   Prevents partial updates
-   Clear error messages for invalid input
-   Fast failure for invalid requests

### Pattern 4: Fail-Safe Authorization

**Implementation:** `BasePolicy::hasPermission()`

```php
try {
    return $user->hasPermission($permission);
} catch (\Throwable $e) {
    Log::error('Permission check failed in policy', [...]);
    return false; // Fail-safe: deny access on error
}
```

**Behavior:**

-   Errors in permission checks deny access (secure default)
-   Never grants access on error
-   Logs error for investigation
-   Maintains security posture

## Logging Strategy

### Log Levels

Following PSR-3 standard:

-   **DEBUG**: Cache hits/misses, permission checks (verbose)
-   **INFO**: Permission assignments, role changes, successful operations
-   **WARNING**: Cache failures, invalid permission attempts, authorization failures
-   **ERROR**: Transaction failures, database errors, service failures
-   **CRITICAL**: Audit logging failures, security breaches, system-wide issues

### Log Context

All logs include:

-   Timestamp (ISO 8601 format)
-   User ID (if authenticated)
-   IP address (for security events)
-   User agent (for security events)
-   Operation-specific context

### Example Log Entries

**Permission Check (DEBUG):**

```json
{
    "level": "debug",
    "message": "Permission check",
    "context": {
        "user_id": 123,
        "permission": "members.view",
        "granted": true,
        "cached": true,
        "timestamp": "2025-12-04T10:30:00Z"
    }
}
```

**Permission Assignment (INFO):**

```json
{
    "level": "info",
    "message": "Permissions assigned to role",
    "context": {
        "role": "DIRECTOR",
        "permission_count": 5,
        "admin_user_id": 1,
        "ip_address": "192.168.1.100",
        "timestamp": "2025-12-04T10:30:00Z"
    }
}
```

**Authorization Failure (WARNING):**

```json
{
    "level": "warning",
    "message": "Authorization failure",
    "context": {
        "user_id": 456,
        "action": "delete",
        "resource": "member",
        "ip_address": "192.168.1.200",
        "user_agent": "Mozilla/5.0...",
        "timestamp": "2025-12-04T10:30:00Z"
    }
}
```

**Audit Logging Failure (CRITICAL):**

```json
{
    "level": "critical",
    "message": "Failed to log permission change - SECURITY AUDIT FAILURE",
    "context": {
        "user_id": 1,
        "role": "DIRECTOR",
        "permissions": ["members.view", "members.create"],
        "error": "Database connection lost",
        "trace": "...",
        "timestamp": "2025-12-04T10:30:00Z"
    }
}
```

## Error Monitoring Integration

### ErrorMonitoringService

**Location:** `app/Services/ErrorMonitoringService.php`

**Purpose:** Provides integration points for external monitoring services.

**Features:**

-   Critical error reporting
-   Security event tracking
-   Cache performance metrics
-   Performance monitoring
-   Health checks

**Integration Points:**

1. **Sentry** (commented out, ready to enable):

```php
if (app()->bound('sentry')) {
    app('sentry')->captureException($exception, [
        'extra' => $errorData,
        'level' => 'critical',
    ]);
}
```

2. **Bugsnag** (commented out, ready to enable):

```php
if (app()->bound('bugsnag')) {
    app('bugsnag')->notifyException($exception, function ($report) use ($errorData) {
        $report->setSeverity('error');
        $report->setMetaData($errorData);
    });
}
```

3. **Custom Webhooks** (ready to implement):

```php
$this->sendToWebhook($errorData);
```

### Health Check Endpoint

The `ErrorMonitoringService::healthCheck()` method provides system health status:

```php
$health = app(ErrorMonitoringService::class)->healthCheck();
```

**Returns:**

```json
{
    "status": "healthy",
    "checks": {
        "cache": {
            "status": "ok",
            "stats": {
                "hits": 1000,
                "misses": 100,
                "hit_rate": 90.91
            }
        },
        "database": {
            "status": "ok"
        },
        "audit_log": {
            "status": "ok",
            "recent_logs": 50
        }
    },
    "timestamp": "2025-12-04T10:30:00Z"
}
```

## RbacLogger Helper

**Location:** `app/Helpers/RbacLogger.php`

**Purpose:** Provides consistent logging patterns for RBAC operations.

**Methods:**

1. `logPermissionCheck()` - Log permission checks
2. `logCacheOperation()` - Log cache operations
3. `logPermissionAssignment()` - Log permission assignments
4. `logRoleChange()` - Log role changes
5. `logAuthorizationFailure()` - Log authorization failures
6. `logSecurityEvent()` - Log security events
7. `logError()` - Log errors with full context
8. `logCritical()` - Log critical errors
9. `logPerformance()` - Log performance metrics
10. `logAuditEvent()` - Log audit trail events

**Usage Example:**

```php
use App\Helpers\RbacLogger;

RbacLogger::logPermissionCheck(
    userId: $user->id,
    permission: 'members.view',
    granted: true,
    cached: true
);
```

## Error Recovery Procedures

### Cache Failure Recovery

1. **Automatic:** System falls back to database queries
2. **Manual:** Clear cache and restart cache service
3. **Monitoring:** Track cache error rate in metrics

### Database Transaction Failure

1. **Automatic:** Transaction rolled back, no partial updates
2. **Manual:** Review error logs, fix underlying issue
3. **User Action:** Retry operation after issue resolved

### Audit Logging Failure

1. **Automatic:** Logged critically but doesn't block operation
2. **Manual:** Investigate immediately (security concern)
3. **Monitoring:** Should trigger alerts in production

## Best Practices

### 1. Always Use Try-Catch in Service Methods

```php
public function someOperation(): void
{
    try {
        // Operation logic
    } catch (\Throwable $e) {
        Log::error('Operation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw new CustomException('Operation failed', $e);
    }
}
```

### 2. Graceful Degradation for Non-Critical Operations

```php
try {
    $this->cacheManager->cacheUserPermissions($userId, $permissions);
} catch (\Throwable $e) {
    // Log but don't fail - cache is not critical
    Log::warning('Cache write failed', ['error' => $e->getMessage()]);
}
```

### 3. Fail-Safe for Security Operations

```php
try {
    return $user->hasPermission($permission);
} catch (\Throwable $e) {
    Log::error('Permission check failed', ['error' => $e->getMessage()]);
    return false; // Deny access on error
}
```

### 4. Include Full Context in Logs

```php
Log::error('Operation failed', [
    'user_id' => $user->id,
    'operation' => 'permission_assignment',
    'role' => $role->value,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString(),
    'ip_address' => request()->ip(),
    'timestamp' => now()->toIso8601String(),
]);
```

### 5. Use Appropriate Log Levels

-   Use DEBUG for verbose operational details
-   Use INFO for successful operations
-   Use WARNING for recoverable errors
-   Use ERROR for operation failures
-   Use CRITICAL for security issues and system failures

## Monitoring Checklist

-   [ ] Cache hit rate > 90%
-   [ ] Average permission check time < 10ms
-   [ ] No critical errors in last 24 hours
-   [ ] Audit log system operational
-   [ ] Database connection healthy
-   [ ] No failed authorization attempts spike
-   [ ] Error rate < 1% of total operations

## Troubleshooting

### High Cache Error Rate

**Symptoms:** Many cache-related warnings in logs

**Causes:**

-   Redis/Memcached service down
-   Network connectivity issues
-   Cache storage full

**Resolution:**

1. Check cache service status
2. Verify network connectivity
3. Clear old cache entries
4. Increase cache storage if needed

### Permission Check Failures

**Symptoms:** Users unable to access resources they should have access to

**Causes:**

-   Database connection issues
-   Cache inconsistency
-   Permission not assigned correctly

**Resolution:**

1. Check database connectivity
2. Clear user permission cache
3. Verify role permissions in database
4. Review audit logs for recent changes

### Audit Logging Failures

**Symptoms:** Critical logs about audit logging failures

**Causes:**

-   Database connection issues
-   Disk space full
-   Database table corruption

**Resolution:**

1. Check database connectivity immediately
2. Verify disk space
3. Check audit_logs table integrity
4. Review recent database changes

## Security Considerations

1. **Never expose sensitive information in error messages**

    - Use generic messages for users
    - Log detailed information server-side

2. **Always log security events**

    - Authorization failures
    - Permission changes
    - Role changes
    - Suspicious activity

3. **Monitor for patterns**

    - Multiple failed authorization attempts
    - Unusual permission changes
    - Access attempts outside normal hours

4. **Fail-safe defaults**
    - Deny access on error
    - Never grant permissions on failure
    - Maintain security posture during degradation

## Production Deployment

### Pre-Deployment Checklist

-   [ ] Configure external monitoring service (Sentry, Bugsnag, etc.)
-   [ ] Set up log aggregation (ELK, Splunk, CloudWatch, etc.)
-   [ ] Configure alerting for critical errors
-   [ ] Test error handling in staging environment
-   [ ] Verify cache fallback behavior
-   [ ] Review log retention policies
-   [ ] Set up health check monitoring

### Post-Deployment Monitoring

-   Monitor error rates for first 24 hours
-   Review log patterns for anomalies
-   Verify cache performance metrics
-   Check audit log completeness
-   Test error recovery procedures

## Conclusion

This comprehensive error handling and logging implementation ensures:

-   System reliability through graceful degradation
-   Security through fail-safe defaults
-   Observability through detailed logging
-   Maintainability through consistent patterns
-   Compliance through audit trail completeness
