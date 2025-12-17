<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Load roles from database (system + custom roles)
        $roles = Role::orderBy('is_system', 'desc')
            ->orderBy('title')
            ->get();

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
     * Store a newly created permission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'unique:permissions,key', 'regex:/^[a-z_]+\.[a-z_]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'module' => ['required', 'string', 'max:255'],
        ]);

        try {
            $permission = Permission::create([
                'key' => $validated['key'],
                'name' => $validated['name'],
                'module' => $validated['module'],
                'is_active' => true,
            ]);

            // Log audit trail
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'permission_created',
                'model_type' => Permission::class,
                'model_id' => $permission->id,
                'description' => sprintf(
                    'Created permission: %s (%s)',
                    $validated['key'],
                    $validated['name']
                ),
                'ip_address' => $request->ip(),
            ]);

            Log::info('Permission created', [
                'permission_key' => $validated['key'],
                'admin_user_id' => Auth::id(),
            ]);

            return back()->with('success', __('Permission created successfully.'));
        } catch (\Throwable $e) {
            Log::error('Failed to create permission', [
                'permission_key' => $validated['key'],
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => __('Failed to create permission. Please try again.')]);
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

        // Get all roles from database
        $roles = Role::all();

        foreach ($roles as $role) {
            // Get permissions for this role from role_permissions table
            $permissions = DB::table('role_permissions')
                ->where('role', $role->code)
                ->pluck('permission_id')
                ->toArray();

            // Get permission keys
            $permissionKeys = Permission::whereIn('id', $permissions)
                ->pluck('key')
                ->toArray();

            $matrix[$role->code] = $permissionKeys;
        }

        return $matrix;
    }
}
