<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * RBAC Logging Helper
 *
 * Provides consistent logging patterns for RBAC operations
 * with appropriate log levels and context
 */
class RbacLogger
{
    /**
     * Log levels as defined in PSR-3
     */
    public const LEVEL_DEBUG = 'debug';

    public const LEVEL_INFO = 'info';

    public const LEVEL_WARNING = 'warning';

    public const LEVEL_ERROR = 'error';

    public const LEVEL_CRITICAL = 'critical';

    /**
     * Log a permission check
     */
    public static function logPermissionCheck(
        int $userId,
        string $permission,
        bool $granted,
        bool $cached = false
    ): void {
        Log::debug('Permission check', [
            'user_id' => $userId,
            'permission' => $permission,
            'granted' => $granted,
            'cached' => $cached,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log a cache operation
     */
    public static function logCacheOperation(
        string $operation,
        string $level = self::LEVEL_DEBUG,
        array $context = []
    ): void {
        Log::log($level, "Cache operation: {$operation}", array_merge($context, [
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log a permission assignment
     */
    public static function logPermissionAssignment(
        int $adminUserId,
        string $role,
        array $permissions,
        bool $success = true
    ): void {
        $level = $success ? self::LEVEL_INFO : self::LEVEL_ERROR;

        Log::log($level, 'Permission assignment', [
            'admin_user_id' => $adminUserId,
            'role' => $role,
            'permission_count' => count($permissions),
            'permissions' => $permissions,
            'success' => $success,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log a role change
     */
    public static function logRoleChange(
        int $adminUserId,
        int $targetUserId,
        string $oldRole,
        string $newRole,
        bool $success = true
    ): void {
        $level = $success ? self::LEVEL_INFO : self::LEVEL_ERROR;

        Log::log($level, 'Role change', [
            'admin_user_id' => $adminUserId,
            'target_user_id' => $targetUserId,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'success' => $success,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log an authorization failure
     */
    public static function logAuthorizationFailure(
        int $userId,
        string $action,
        string $resource,
        ?string $reason = null
    ): void {
        Log::warning('Authorization failure', [
            'user_id' => $userId,
            'action' => $action,
            'resource' => $resource,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log a security event
     */
    public static function logSecurityEvent(
        string $event,
        string $level = self::LEVEL_WARNING,
        array $context = []
    ): void {
        Log::log($level, "Security event: {$event}", array_merge($context, [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log an error with full context
     */
    public static function logError(
        string $message,
        \Throwable $exception,
        array $context = []
    ): void {
        Log::error($message, array_merge($context, [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log a critical error that requires immediate attention
     */
    public static function logCritical(
        string $message,
        \Throwable $exception,
        array $context = []
    ): void {
        Log::critical($message, array_merge($context, [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]));

        // Trigger alert for critical errors
        // This is where you'd integrate with alerting services
    }

    /**
     * Log performance metrics
     */
    public static function logPerformance(
        string $operation,
        float $duration,
        array $context = []
    ): void {
        $durationMs = round($duration * 1000, 2);
        $level = $durationMs > 100 ? self::LEVEL_WARNING : self::LEVEL_DEBUG;

        Log::log($level, "Performance: {$operation}", array_merge($context, [
            'duration_ms' => $durationMs,
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log audit trail event
     */
    public static function logAuditEvent(
        string $action,
        string $targetType,
        $targetId,
        array $changes = []
    ): void {
        Log::info('Audit event', [
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
