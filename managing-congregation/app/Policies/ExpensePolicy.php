<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super Admin can do everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        // General (Treasurer) and Directors can view expenses
        return $user->hasRole(UserRole::GENERAL) || $user->hasRole(UserRole::DIRECTOR);
    }

    /**
     * Determine whether the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        // General (Treasurer) can view all expenses across all communities
        if ($user->hasRole(UserRole::GENERAL)) {
            return true;
        }

        // Directors can only view expenses from their own community
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create expenses.
     */
    public function create(User $user): bool
    {
        // Only Directors can create expenses (not General Treasurer)
        // General has read-only access
        return $user->hasRole(UserRole::DIRECTOR);
    }

    /**
     * Determine whether the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        // General (Treasurer) has read-only access - cannot update
        if ($user->hasRole(UserRole::GENERAL)) {
            return false;
        }

        // Directors can only update expenses from their own community
        // and only if the expense is not locked
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id && ! $expense->is_locked;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // General (Treasurer) has read-only access - cannot delete
        if ($user->hasRole(UserRole::GENERAL)) {
            return false;
        }

        // Directors can only delete expenses from their own community
        // and only if the expense is not locked
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id && ! $expense->is_locked;
        }

        return false;
    }

    /**
     * Determine whether the user can lock financial periods.
     */
    public function lockPeriod(User $user): bool
    {
        // Only General (Treasurer) can lock periods
        // Directors cannot lock their own periods
        return $user->hasRole(UserRole::GENERAL);
    }

    /**
     * Determine whether the user can view financial reports.
     */
    public function viewReports(User $user): bool
    {
        // Both General (Treasurer) and Directors can view reports
        return $user->hasRole(UserRole::GENERAL) || $user->hasRole(UserRole::DIRECTOR);
    }

    /**
     * Determine whether the user can export financial reports.
     */
    public function exportReports(User $user): bool
    {
        // Both General (Treasurer) and Directors can export reports
        return $user->hasRole(UserRole::GENERAL) || $user->hasRole(UserRole::DIRECTOR);
    }
}
