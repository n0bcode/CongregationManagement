<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleManagementController extends Controller
{
    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:roles,code', 'regex:/^[a-z_]+$/', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Create the role
                $role = Role::create([
                    'code' => $validated['code'],
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'is_system' => false, // Custom roles are never system roles
                ]);

                // Assign permissions to the role
                if (!empty($validated['permissions'])) {
                    $assignments = [];
                    foreach ($validated['permissions'] as $permissionId) {
                        $assignments[] = [
                            'role' => $role->code,
                            'permission_id' => $permissionId,
                        ];
                    }
                    DB::table('role_permissions')->insert($assignments);
                }

                // Log audit trail
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'role_created',
                    'model_type' => Role::class,
                    'model_id' => $role->id,
                    'description' => sprintf(
                        'Created custom role: %s (%s) with %d permissions',
                        $validated['code'],
                        $validated['title'],
                        count($validated['permissions'] ?? [])
                    ),
                    'ip_address' => $request->ip(),
                ]);

                Log::info('Custom role created', [
                    'role_code' => $validated['code'],
                    'admin_user_id' => Auth::id(),
                ]);
            });

            return back()->with('success', __('Role created successfully.'));
        } catch (\Throwable $e) {
            Log::error('Failed to create role', [
                'role_code' => $validated['code'],
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => __('Failed to create role. Please try again.')]);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deletion of system roles
        if ($role->is_system) {
            return back()->withErrors(['error' => __('System roles cannot be deleted.')]);
        }

        try {
            DB::transaction(function () use ($role) {
                // Remove role permissions
                DB::table('role_permissions')->where('role', $role->code)->delete();

                // Log audit trail
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'role_deleted',
                    'model_type' => Role::class,
                    'model_id' => $role->id,
                    'description' => sprintf(
                        'Deleted custom role: %s (%s)',
                        $role->code,
                        $role->title
                    ),
                    'ip_address' => request()->ip(),
                ]);

                // Delete the role
                $role->delete();

                Log::info('Custom role deleted', [
                    'role_code' => $role->code,
                    'admin_user_id' => Auth::id(),
                ]);
            });

            return back()->with('success', __('Role deleted successfully.'));
        } catch (\Throwable $e) {
            Log::error('Failed to delete role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => __('Failed to delete role. Please try again.')]);
        }
    }
}
