<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

class ChartService
{
    /**
     * Prepare data for an expense trend chart (line chart).
     */
    public function prepareExpenseTrendData(Collection $dailyBreakdown): array
    {
        $labels = $dailyBreakdown->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        })->toArray();

        $data = $dailyBreakdown->pluck('total')->map(function ($amount) {
            return $amount / 100; // Convert to dollars
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Daily Expenses',
                    'data' => $data,
                    'borderColor' => '#d97706', // amber-600
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Prepare data for a category distribution chart (doughnut chart).
     */
    public function prepareCategoryDistributionData(Collection $byCategory): array
    {
        $labels = $byCategory->pluck('category')->toArray();
        $data = $byCategory->pluck('total')->map(function ($amount) {
            return $amount / 100;
        })->toArray();

        // Generate colors (using a predefined palette or random generation)
        $colors = [
            '#ef4444', // red-500
            '#f97316', // orange-500
            '#f59e0b', // amber-500
            '#84cc16', // lime-500
            '#10b981', // emerald-500
            '#06b6d4', // cyan-500
            '#3b82f6', // blue-500
            '#6366f1', // indigo-500
            '#8b5cf6', // violet-500
            '#d946ef', // fuchsia-500
            '#f43f5e', // rose-500
            '#64748b', // slate-500
        ];

        // Ensure we have enough colors
        $backgroundColors = array_slice($colors, 0, count($data));

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }
}
