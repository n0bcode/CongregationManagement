<?php

namespace App\Livewire;

use App\Models\Community;
use App\Services\ChartService;
use App\Services\FinancialService;
use Livewire\Component;

class FinancialDashboard extends Component
{
    public $year;
    public $month;
    public $communityId;

    public function mount()
    {
        $this->year = now()->year;
        $this->month = now()->month;
        // Default to first community or null if user is admin, or user's community
        $this->communityId = auth()->user()->community_id ?? Community::first()?->id;
    }

    public function render()
    {
        $financialService = app(FinancialService::class);
        $chartService = app(ChartService::class);

        $report = $financialService->generateMonthlyReport(
            (int) $this->communityId,
            (int) $this->year,
            (int) $this->month
        );

        $trendChartData = $chartService->prepareExpenseTrendData($report['daily_breakdown']);
        $categoryChartData = $chartService->prepareCategoryDistributionData($report['by_category']);

        $this->dispatch('charts-updated', trend: $trendChartData, category: $categoryChartData);

        return view('livewire.financial-dashboard', [
            'report' => $report,
            'trendChartData' => $trendChartData,
            'categoryChartData' => $categoryChartData,
            'communities' => Community::orderBy('name')->get(),
            'years' => range(now()->year, 2020),
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ],
        ]);
    }
}
