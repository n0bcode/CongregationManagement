<?php

namespace App\View\Components\Widgets;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UpcomingEventsWidget extends BaseWidget
{
    public function render(): View
    {
        return view('components.widgets.upcoming-events', [
            'data' => $this->getData(),
        ]);
    }

    public function getData(): array
    {
        // Use NotificationService logic or direct query
        return [
            'events' => Reminder::where('reminder_date', '>=', now())
                ->where('reminder_date', '<=', now()->addDays(30))
                ->orderBy('reminder_date')
                ->take(5)
                ->get(),
        ];
    }
}
