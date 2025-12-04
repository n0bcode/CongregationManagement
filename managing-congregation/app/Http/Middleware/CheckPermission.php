<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has the required permission(s).
     * Supports multiple permissions separated by '|' (OR) or '&' (AND).
     *
     * Usage:
     * - Route::get('/members', ...)->middleware('permission:members.view');
     * - Route::get('/members', ...)->middleware('permission:members.view|members.create');
     * - Route::get('/members', ...)->middleware('permission:members.view&members.edit');
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions  One or more permission keys
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', __('You must be logged in to access this page.'));
        }

        $user = Auth::user();

        // If no permissions specified, just check authentication
        if (empty($permissions)) {
            return $next($request);
        }

        // Check each permission requirement
        foreach ($permissions as $permissionString) {
            // Handle OR logic (|)
            if (str_contains($permissionString, '|')) {
                $orPermissions = explode('|', $permissionString);
                $hasAny = false;

                foreach ($orPermissions as $permission) {
                    if ($user->hasPermission(trim($permission))) {
                        $hasAny = true;
                        break;
                    }
                }

                if (! $hasAny) {
                    return $this->unauthorized($request);
                }

                continue;
            }

            // Handle AND logic (&)
            if (str_contains($permissionString, '&')) {
                $andPermissions = explode('&', $permissionString);

                foreach ($andPermissions as $permission) {
                    if (! $user->hasPermission(trim($permission))) {
                        return $this->unauthorized($request);
                    }
                }

                continue;
            }

            // Single permission check
            if (! $user->hasPermission($permissionString)) {
                return $this->unauthorized($request);
            }
        }

        return $next($request);
    }

    /**
     * Handle unauthorized access
     */
    private function unauthorized(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('You do not have permission to access this resource.'),
            ], 403);
        }

        abort(403, __('You do not have permission to access this resource.'));
    }
}
