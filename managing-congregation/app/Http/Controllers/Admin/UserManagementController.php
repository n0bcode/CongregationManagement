<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request): View
    {
        $query = User::query()->with('community');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('name')->paginate(20);
        $roles = UserRole::cases();
        $communities = Community::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'communities'));
    }

    /**
     * Update the specified user's role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Prevent users from changing their own role
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => __('You cannot change your own role.')]);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(array_column(UserRole::cases(), 'value'))],
            'community_id' => ['nullable', 'exists:communities,id'],
        ]);

        $oldRole = $user->role?->value;
        $newRole = $validated['role'];

        // Update user role
        $user->role = UserRole::from($newRole);

        // Update community if provided (required for Directors)
        if ($newRole === UserRole::DIRECTOR->value) {
            if (empty($validated['community_id'])) {
                return back()->withErrors(['community_id' => __('Community is required for Director role.')]);
            }
            $user->community_id = $validated['community_id'];
        } else {
            // Clear community for non-Director roles
            $user->community_id = null;
        }

        $user->save();

        // Log the role change in audit trail
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_role_changed',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => sprintf(
                'Changed role for user %s (ID: %d) from %s to %s',
                $user->email,
                $user->id,
                $oldRole ?? 'none',
                $newRole
            ),
            'ip_address' => $request->ip(),
        ]);

        // Invalidate user's permission cache
        try {
            $cacheManager = app(\App\Contracts\CacheManagerInterface::class);
            $cacheManager->invalidateUserCache($user->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to invalidate user cache after role change', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', __('User role updated successfully.'));
    }
}
