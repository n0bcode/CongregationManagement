<?php

namespace App\View\Components\Widgets;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormationProgressWidget extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.widgets.formation-progress-widget', [
            'data' => $this->getData(),
        ]);
    }

    public function getData(): array
    {
        // Get the latest formation event for each member
        // We use a subquery to find the latest event ID for each member
        $latestEvents = \App\Models\FormationEvent::query()
            ->select('stage')
            ->whereIn('id', function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('MAX(id)'))
                    ->from('formation_events')
                    ->groupBy('member_id');
            })
            ->get();

        $stages = $latestEvents->groupBy('stage')->map->count()->toArray();

        return [
            'stages' => $stages,
            'total_in_formation' => array_sum($stages),
        ];
    }
}
