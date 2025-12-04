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
        return $user->hasPermission('financials.view');
    }

    /**
     * Determine whether the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        if (! $user->hasPermission('financials.view')) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id;
        }

        // General and Super Admin can view all
        return true;
    }

    /**
     * Determine whether the user can create expenses.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('financials.create');
    }

    /**
     * Determine whether the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        if (! $user->hasPermission('financials.create')) {
            return false;
        }

        // Cannot update locked expenses
        if ($expense->is_locked) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        if (! $user->hasPermission('financials.manage')) {
            return false;
        }

        // Cannot delete locked expenses
        if ($expense->is_locked) {
            return false;
        }

        // Community scoping for Directors
        if ($user->hasRole(UserRole::DIRECTOR)) {
            return $user->community_id === $expense->community_id;
        }

        return true;
    }

    /**
     * Determine whether the user can approve expenses.
     */
    public function approve(User $user): bool
    {
        return $user->hasPermission('financials.approve');
    }

    /**
     * Determine whether the user can lock financial periods.
     */
    public function lockPeriod(User $user): bool
    {
        return $user->hasPermission('financials.manage');
    }

    /**
     * Determine whether the user can view financial reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->hasPermission('financials.view');
    }

    /**
     * Determine whether the user can export financial reports.
     */
    public function exportReports(User $user): bool
    {
        return $user->hasPermission('financials.export');
    }
}
