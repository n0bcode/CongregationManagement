<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Assign permissions to a role, replacing any existing permissions.
     */
    public function assignPermissionsToRole(UserRole $role, array $permissionKeys): void
    {
        // Remove old permissions for this role
        DB::table('role_permissions')
            ->where('role', $role->value)
            ->delete();

        // Get permission IDs for the given keys
        $validPermissions = Permission::whereIn('key', $permissionKeys)->get();
        $validKeys = $validPermissions->pluck('key')->toArray();

        // Log warning for invalid permission keys
        $invalidKeys = array_diff($permissionKeys, $validKeys);
        if (! empty($invalidKeys)) {
            \Illuminate\Support\Facades\Log::warning('Invalid permission keys provided to assignPermissionsToRole', [
                'role' => $role->value,
                'invalid_keys' => $invalidKeys,
            ]);
        }

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
    }

    /**
     * Get all permissions assigned to a role.
     */
    public function getRolePermissions(UserRole $role): Collection
    {
        return Permission::join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $role->value)
            ->select('permissions.*')
            ->get();
    }
}
