<?php

namespace App\Livewire;

use App\Services\DashboardService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(DashboardService $dashboardService)
    {
        $widgets = $dashboardService->getWidgetsForUser(auth()->user());

        return view('livewire.dashboard', [
            'widgets' => $widgets,
        ])->layout('layouts.app');
    }

    public function updateWidgetOrder($list)
    {
        foreach ($list as $item) {
            \App\Models\DashboardWidget::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'widget_type' => $item['value'],
                ],
                [
                    'position' => $item['order'],
                ]
            );
        }
    }
}
