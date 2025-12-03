<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait ScopedByCommunity
 *
 * Automatically filters queries based on the authenticated user's community.
 *
 * USAGE:
 * use App\Models\Concerns\ScopedByCommunity;
 * class Member extends Model { use ScopedByCommunity; }
 *
 * ROLES:
 * - SUPER_ADMIN / GENERAL: Bypassed (sees all records)
 * - DIRECTOR / MEMBER: Scoped to their assigned community_id
 * - Unauthenticated: Blocked (sees no records)
 *
 * BYPASSING:
 * To temporarily disable the scope (e.g., in console commands):
 * Member::withoutGlobalScopes()->get();
 */
trait ScopedByCommunity
{
    /**
     * Boot the scoped by community trait for a model.
     *
     * PATTERN: Anonymous Global Scope
     * APPLIES TO: Director and Member roles
     * BYPASSES: Super Admin and General roles
     */
    protected static function bootScopedByCommunity(): void
    {
        // Use anonymous scope (no name) for simplicity
        static::addGlobalScope(function (Builder $builder) {
            // STEP 1: Get authenticated user
            $user = Auth::user();

            // STEP 2: Handle unauthenticated requests (console commands, API)
            // SECURE-BY-DEFAULT: Block access for unauthenticated context
            // Console commands/Seeders must explicitly use withoutGlobalScopes()
            if (! $user) {
                $builder->whereRaw('1 = 0');

                return;
            }

            // STEP 3: Bypass scoping for privileged roles
            // Use helper methods from User model for consistency
            if ($user->isSuperAdmin() || $user->hasRole(UserRole::GENERAL)) {
                return;
            }

            // STEP 4: Apply community scoping for Director and Member roles
            // Both roles are restricted to their assigned community
            if ($user->community_id) {
                $builder->where('community_id', $user->community_id);
            } else {
                // If user has no community assigned, they should see NOTHING
                // Prevent access to any records
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
