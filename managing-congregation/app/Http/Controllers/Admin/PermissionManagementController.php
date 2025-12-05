<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PermissionManagementController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {
        // Only super admins can access permission management
        // $this->middleware('can:view-admin');
    }

    /**
     * Display permission management page
     */
    public function index(): View
    {
        $roles = UserRole::cases();

        // Get all permissions grouped by module
        $permissions = Permission::orderBy('module')
            ->orderBy('key')
            ->get()
            ->groupBy('module');

        // Build permission matrix (role => [permission_keys])
        $rolePermissions = $this->buildPermissionMatrix();

        return view('admin.permissions.index', compact(
            'roles',
            'permissions',
            'rolePermissions'
        ));
    }

    /**
     * Update permissions for a role
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,key'],
        ]);

        try {
            $role = UserRole::from($validated['role']);

            $this->permissionService->assignPermissionsToRole(
                $role,
                $validated['permissions']
            );

            return back()->with('success', __('Permissions updated successfully for :role role.', [
                'role' => $role->name,
            ]));
        } catch (\ValueError $e) {
            return back()->withErrors(['role' => __('Invalid role specified.')]);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => __('Failed to update permissions. Please try again.')]);
        }
    }

    /**
     * Sync permissions from routes
     */
    public function sync(): RedirectResponse
    {
        try {
            // Run the sync command programmatically
            \Illuminate\Support\Facades\Artisan::call('permissions:sync', ['--force' => true]);

            $output = \Illuminate\Support\Facades\Artisan::output();

            // Extract statistics from output
            preg_match('/Created (\d+) new permissions/', $output, $newMatches);
            preg_match('/Updated (\d+) permissions/', $output, $updatedMatches);
            preg_match('/Marked (\d+) permissions as inactive/', $output, $orphanedMatches);

            $new = $newMatches[1] ?? 0;
            $updated = $updatedMatches[1] ?? 0;
            $orphaned = $orphanedMatches[1] ?? 0;

            $message = __('Permissions synced successfully! Created: :new, Updated: :updated, Marked inactive: :orphaned', [
                'new' => $new,
                'updated' => $updated,
                'orphaned' => $orphaned,
            ]);

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => __('Failed to sync permissions. Please try again.')]);
        }
    }

    /**
     * Display audit log page
     */
    public function audit(): View
    {
        $logs = AuditLog::permissionActions()
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('admin.permissions.audit', compact('logs'));
    }

    /**
     * Build permission matrix for all roles
     */
    private function buildPermissionMatrix(): array
    {
        $matrix = [];

        foreach (UserRole::cases() as $role) {
            $permissions = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', $role->value)
                ->pluck('permissions.key')
                ->toArray();

            $matrix[$role->value] = $permissions;
        }

        return $matrix;
    }
}
