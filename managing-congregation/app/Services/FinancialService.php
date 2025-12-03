<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Generate a monthly financial report for a community.
     */
    public function generateMonthlyReport(int $communityId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all expenses for the period
        $expenses = Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->with(['creator'])
            ->orderBy('date')
            ->get();

        // Aggregate by category
        $byCategory = $this->aggregateExpensesByCategory($expenses);

        // Calculate totals
        $totalAmount = $expenses->sum('amount');
        $totalCount = $expenses->count();

        // Get daily breakdown
        $dailyBreakdown = $expenses->groupBy(function ($expense) {
            return $expense->date->format('Y-m-d');
        })->map(function ($dayExpenses) {
            return [
                'date' => $dayExpenses->first()->date->format('Y-m-d'),
                'count' => $dayExpenses->count(),
                'total' => $dayExpenses->sum('amount'),
            ];
        })->values();

        return [
            'community_id' => $communityId,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'month_name' => $startDate->format('F Y'),
            ],
            'summary' => [
                'total_amount' => $totalAmount,
                'total_amount_dollars' => $totalAmount / 100,
                'total_count' => $totalCount,
                'average_expense' => $totalCount > 0 ? $totalAmount / $totalCount : 0,
                'average_expense_dollars' => $totalCount > 0 ? ($totalAmount / $totalCount) / 100 : 0,
            ],
            'by_category' => $byCategory,
            'daily_breakdown' => $dailyBreakdown,
            'expenses' => $expenses,
            'generated_at' => now()->toDateTimeString(),
            'generated_by' => Auth::id(),
        ];
    }

    /**
     * Aggregate expenses by category.
     */
    public function aggregateExpensesByCategory(Collection $expenses): Collection
    {
        return $expenses->groupBy('category')
            ->map(function ($categoryExpenses, $category) {
                $total = $categoryExpenses->sum('amount');
                $count = $categoryExpenses->count();

                return [
                    'category' => $category,
                    'count' => $count,
                    'total' => $total,
                    'total_dollars' => $total / 100,
                    'average' => $count > 0 ? $total / $count : 0,
                    'average_dollars' => $count > 0 ? ($total / $count) / 100 : 0,
                    'percentage' => 0, // Will be calculated after we have grand total
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->tap(function ($categories) {
                // Calculate percentages
                $grandTotal = $categories->sum('total');
                if ($grandTotal > 0) {
                    $categories->transform(function ($category) use ($grandTotal) {
                        $category['percentage'] = ($category['total'] / $grandTotal) * 100;

                        return $category;
                    });
                }
            });
    }

    /**
     * Lock expenses for a specific period.
     */
    public function lockPeriod(int $communityId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all unlocked expenses for the period
        $expenses = Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->unlocked()
            ->get();

        $lockedCount = 0;
        $lockedAmount = 0;

        DB::transaction(function () use ($expenses, &$lockedCount, &$lockedAmount) {
            foreach ($expenses as $expense) {
                $expense->update([
                    'is_locked' => true,
                    'locked_at' => now(),
                    'locked_by' => Auth::id(),
                ]);

                $lockedCount++;
                $lockedAmount += $expense->amount;
            }
        });

        return [
            'success' => true,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'month_name' => $startDate->format('F Y'),
            ],
            'locked_count' => $lockedCount,
            'locked_amount' => $lockedAmount,
            'locked_amount_dollars' => $lockedAmount / 100,
            'locked_at' => now()->toDateTimeString(),
            'locked_by' => Auth::id(),
        ];
    }

    /**
     * Check if a period is locked.
     */
    public function isPeriodLocked(int $communityId, int $year, int $month): bool
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Check if any expenses in the period are locked
        return Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->locked()
            ->exists();
    }

    /**
     * Get period lock status.
     */
    public function getPeriodLockStatus(int $communityId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $totalExpenses = Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->count();

        $lockedExpenses = Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->locked()
            ->count();

        $isFullyLocked = $totalExpenses > 0 && $totalExpenses === $lockedExpenses;
        $isPartiallyLocked = $lockedExpenses > 0 && $lockedExpenses < $totalExpenses;

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'month_name' => $startDate->format('F Y'),
            ],
            'total_expenses' => $totalExpenses,
            'locked_expenses' => $lockedExpenses,
            'unlocked_expenses' => $totalExpenses - $lockedExpenses,
            'is_fully_locked' => $isFullyLocked,
            'is_partially_locked' => $isPartiallyLocked,
            'lock_percentage' => $totalExpenses > 0 ? ($lockedExpenses / $totalExpenses) * 100 : 0,
        ];
    }

    /**
     * Get year-to-date summary for a community.
     */
    public function getYearToDateSummary(int $communityId, int $year): array
    {
        $startDate = Carbon::create($year, 1, 1)->startOfYear();
        $endDate = Carbon::create($year, 12, 31)->endOfYear();

        $expenses = Expense::where('community_id', $communityId)
            ->dateRange($startDate, $endDate)
            ->get();

        $byCategory = $this->aggregateExpensesByCategory($expenses);

        // Monthly breakdown
        $monthlyBreakdown = $expenses->groupBy(function ($expense) {
            return $expense->date->format('Y-m');
        })->map(function ($monthExpenses, $yearMonth) {
            return [
                'year_month' => $yearMonth,
                'month_name' => Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y'),
                'count' => $monthExpenses->count(),
                'total' => $monthExpenses->sum('amount'),
                'total_dollars' => $monthExpenses->sum('amount') / 100,
            ];
        })->values();

        return [
            'community_id' => $communityId,
            'year' => $year,
            'summary' => [
                'total_amount' => $expenses->sum('amount'),
                'total_amount_dollars' => $expenses->sum('amount') / 100,
                'total_count' => $expenses->count(),
                'average_expense' => $expenses->count() > 0 ? $expenses->sum('amount') / $expenses->count() : 0,
                'average_expense_dollars' => $expenses->count() > 0 ? ($expenses->sum('amount') / $expenses->count()) / 100 : 0,
            ],
            'by_category' => $byCategory,
            'monthly_breakdown' => $monthlyBreakdown,
        ];
    }

    /**
     * Compare two periods.
     */
    public function comparePeriods(
        int $communityId,
        int $year1,
        int $month1,
        int $year2,
        int $month2
    ): array {
        $report1 = $this->generateMonthlyReport($communityId, $year1, $month1);
        $report2 = $this->generateMonthlyReport($communityId, $year2, $month2);

        $diff = $report2['summary']['total_amount'] - $report1['summary']['total_amount'];
        $percentChange = $report1['summary']['total_amount'] > 0
            ? ($diff / $report1['summary']['total_amount']) * 100
            : 0;

        return [
            'period1' => $report1,
            'period2' => $report2,
            'comparison' => [
                'amount_difference' => $diff,
                'amount_difference_dollars' => $diff / 100,
                'percent_change' => $percentChange,
                'count_difference' => $report2['summary']['total_count'] - $report1['summary']['total_count'],
            ],
        ];
    }
}
