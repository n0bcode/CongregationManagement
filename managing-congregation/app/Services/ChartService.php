<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ChartService
{
    /**
     * Get monthly expenses for a community for a specific year.
     *
     * @param int $communityId
     * @param int $year
     * @return array
     */
    public function getMonthlyExpenses(int $communityId, int $year): array
    {
        $expenses = Expense::where('community_id', $communityId)
            ->whereYear('date', $year)
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data = array_fill(1, 12, 0);

        foreach ($expenses as $expense) {
            $data[$expense->month] = $expense->total / 100; // Convert to dollars
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Monthly Expenses',
                    'data' => array_values($data),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)', // amber-500
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 1,
                ]
            ]
        ];
    }

    /**
     * Get expenses grouped by category for a community for a specific year.
     *
     * @param int $communityId
     * @param int $year
     * @return array
     */
    public function getExpensesByCategory(int $communityId, int $year): array
    {
        $expenses = Expense::where('community_id', $communityId)
            ->whereYear('date', $year)
            ->select(
                'category',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $expenses->pluck('category')->toArray(),
            'datasets' => [
                [
                    'data' => $expenses->map(fn($e) => $e->total / 100)->toArray(),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.7)',  // red-500
                        'rgba(59, 130, 246, 0.7)', // blue-500
                        'rgba(16, 185, 129, 0.7)', // emerald-500
                        'rgba(245, 158, 11, 0.7)', // amber-500
                        'rgba(139, 92, 246, 0.7)', // violet-500
                        'rgba(236, 72, 153, 0.7)', // pink-500
                    ],
                ]
            ]
        ];
    }

    /**
     * Get budget vs actual expenses for a project.
     *
     * @param int $projectId
     * @return array
     */
    public function getBudgetVsActual(int $projectId): array
    {
        $project = Project::withSum('expenses', 'amount')->find($projectId);

        if (!$project) {
            return [];
        }

        $budget = $project->budget; // Already in dollars/decimal
        $actual = ($project->expenses_sum_amount ?? 0) / 100;

        return [
            'labels' => ['Budget', 'Actual'],
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => [$budget, $actual],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.7)', // blue-500
                        $actual > $budget ? 'rgba(239, 68, 68, 0.7)' : 'rgba(16, 185, 129, 0.7)', // red if over, green if under
                    ],
                ]
            ]
        ];
    }
}
