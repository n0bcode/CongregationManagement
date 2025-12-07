<?php

namespace App\View\Components\Widgets;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class RecentActivityWidget extends BaseWidget
{
    public function render(): View
    {
        return view('components.widgets.recent-activity', [
            'data' => $this->getData(),
        ]);
    }

    public function getData(): array
    {
        return [
            'recentActivity' => AuditLog::with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
