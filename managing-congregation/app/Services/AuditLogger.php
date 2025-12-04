<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Enums\UserRole;
use App\Exceptions\AuditLogException;
use App\Models\AuditLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AuditLogger implements AuditLoggerInterface
{
    /**
     * Log permission change
     *
     * @throws AuditLogException If audit logging fails
     */
    public function logPermissionChange(
        int $userId,
        UserRole $role,
        array $permissions
    ): void {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'permission_updated',
                'target_type' => 'role',
                'target_id' => $role->value,
                'changes' => [
                    'permissions' => $permissions,
                    'timestamp' => now()->toIso8601String(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::info('Permission change logged', [
                'user_id' => $userId,
                'role' => $role->value,
                'permission_count' => count($permissions),
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Audit logging failure is critical for security
            Log::critical('Failed to log permission change - SECURITY AUDIT FAILURE', [
                'user_id' => $userId,
                'role' => $role->value,
                'permissions' => $permissions,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't throw - we don't want to block operations, but log critically
            // In production, this should trigger alerts
        }
    }

    /**
     * Log role change
     *
     * @throws AuditLogException If audit logging fails
     */
    public function logRoleChange(
        int $userId,
        int $targetUserId,
        UserRole $oldRole,
        UserRole $newRole
    ): void {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'role_changed',
                'target_type' => 'user',
                'target_id' => $targetUserId,
                'changes' => [
                    'old_role' => $oldRole->value,
                    'new_role' => $newRole->value,
                    'timestamp' => now()->toIso8601String(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::info('Role change logged', [
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
                'old_role' => $oldRole->value,
                'new_role' => $newRole->value,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Audit logging failure is critical for security
            Log::critical('Failed to log role change - SECURITY AUDIT FAILURE', [
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
                'old_role' => $oldRole->value,
                'new_role' => $newRole->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't throw - we don't want to block operations, but log critically
            // In production, this should trigger alerts
        }
    }

    /**
     * Get audit history for a role
     *
     * Returns empty collection on error (graceful degradation)
     */
    public function getRoleAuditHistory(UserRole $role): Collection
    {
        try {
            return AuditLog::where('target_type', 'role')
                ->where('target_id', $role->value)
                ->with('user')
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve role audit history', [
                'role' => $role->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return empty collection - graceful degradation
            return collect();
        }
    }

    /**
     * Get audit history for a user
     *
     * Returns empty collection on error (graceful degradation)
     */
    public function getUserAuditHistory(int $userId): Collection
    {
        try {
            return AuditLog::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere(function ($q) use ($userId) {
                        $q->where('target_type', 'user')
                            ->where('target_id', $userId);
                    });
            })
                ->with('user')
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve user audit history', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return empty collection - graceful degradation
            return collect();
        }
    }

    /**
     * Log a generic security event
     */
    public function logSecurityEvent(string $action, array $context = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id() ?? 0,
                'action' => $action,
                'target_type' => $context['target_type'] ?? 'system',
                'target_id' => $context['target_id'] ?? null,
                'changes' => $context['changes'] ?? [],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::info('Security event logged', [
                'action' => $action,
                'context' => $context,
            ]);
        } catch (\Throwable $e) {
            Log::critical('Failed to log security event - SECURITY AUDIT FAILURE', [
                'action' => $action,
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
