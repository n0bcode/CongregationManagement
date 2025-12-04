<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuditLoggerInterface;
use App\Contracts\CacheManagerInterface;
use App\Enums\UserRole;
use App\Exceptions\PermissionNotFoundException;
use App\Exceptions\PermissionUpdateException;
use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    public function __construct(
        private ?CacheManagerInterface $cacheManager = null,
        private ?AuditLoggerInterface $auditLogger = null
    ) {
        // Use optional dependencies to avoid circular dependency issues
        $this->cacheManager = $cacheManager ?? app(CacheManagerInterface::class);
        $this->auditLogger = $auditLogger ?? app(AuditLoggerInterface::class);
    }

    /**
     * Assign permissions to a role, replacing any existing permissions.
     *
     * @throws PermissionNotFoundException If any permission keys are invalid
     * @throws PermissionUpdateException If the database transaction fails
     */
    public function assignPermissionsToRole(UserRole $role, array $permissionKeys): void
    {
        // Validate all permissions exist before starting transaction
        $validPermissions = Permission::whereIn('key', $permissionKeys)->get();

        $validKeys = $validPermissions->pluck('key')->toArray();
        $invalidKeys = array_diff($permissionKeys, $validKeys);

        if (! empty($invalidKeys)) {
            Log::warning('Invalid permission keys provided', [
                'role' => $role->value,
                'invalid_keys' => $invalidKeys,
            ]);

            throw new PermissionNotFoundException(implode(', ', $invalidKeys));
        }

        try {
            DB::transaction(function () use ($role, $validPermissions, $validKeys) {
                // Remove old permissions for this role
                DB::table('role_permissions')
                    ->where('role', $role->value)
                    ->delete();

                // Insert new role-permission assignments
                $assignments = $validPermissions->map(function ($permission) use ($role) {
                    return [
                        'role' => $role->value,
                        'permission_id' => $permission->id,
                    ];
                })->toArray();

                if (! empty($assignments)) {
                    DB::table('role_permissions')->insert($assignments);
                }

                // Invalidate cache for all users with this role
                try {
                    $this->cacheManager->invalidateRoleCache($role);
                } catch (\Throwable $cacheError) {
                    // Log but don't fail - cache invalidation is not critical
                    Log::warning('Cache invalidation failed during permission assignment', [
                        'role' => $role->value,
                        'error' => $cacheError->getMessage(),
                    ]);
                }

                // Log audit trail
                if (Auth::check()) {
                    try {
                        $this->auditLogger->logPermissionChange(
                            Auth::id(),
                            $role,
                            $validKeys
                        );
                    } catch (\Throwable $auditError) {
                        // Log but don't fail - audit logging failure is serious but shouldn't block operation
                        Log::error('Audit logging failed during permission assignment', [
                            'role' => $role->value,
                            'error' => $auditError->getMessage(),
                        ]);
                    }
                }

                Log::info('Permissions assigned to role', [
                    'role' => $role->value,
                    'permission_count' => count($validKeys),
                    'admin_user_id' => Auth::id(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Failed to assign permissions to role', [
                'role' => $role->value,
                'permission_keys' => $permissionKeys,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new PermissionUpdateException($role, $e);
        }
    }

    /**
     * Get all permissions assigned to a role.
     */
    public function getRolePermissions(UserRole $role): Collection
    {
        try {
            return Permission::join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', $role->value)
                ->select('permissions.*')
                ->get();
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve role permissions', [
                'role' => $role->value,
                'error' => $e->getMessage(),
            ]);

            // Return empty collection on error - graceful degradation
            return collect();
        }
    }

    /**
     * Check if a permission key exists in the system.
     */
    public function permissionExists(string $permissionKey): bool
    {
        try {
            return Permission::where('key', $permissionKey)->exists();
        } catch (\Throwable $e) {
            Log::error('Failed to check permission existence', [
                'permission_key' => $permissionKey,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get permission usage statistics.
     */
    public function getPermissionStats(): array
    {
        try {
            $totalPermissions = Permission::count();
            $assignedPermissions = DB::table('role_permissions')
                ->distinct('permission_id')
                ->count();
            $unusedPermissions = $totalPermissions - $assignedPermissions;

            return [
                'total' => $totalPermissions,
                'assigned' => $assignedPermissions,
                'unused' => $unusedPermissions,
                'roles' => UserRole::cases(),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve permission statistics', [
                'error' => $e->getMessage(),
            ]);

            return [
                'total' => 0,
                'assigned' => 0,
                'unused' => 0,
                'roles' => [],
                'error' => 'Failed to retrieve statistics',
            ];
        }
    }
}
