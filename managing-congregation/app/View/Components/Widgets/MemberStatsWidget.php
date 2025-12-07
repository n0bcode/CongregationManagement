<?php

namespace App\View\Components\Widgets;

use App\Models\Member;
use App\Models\User;
use Illuminate\Contracts\View\View;

class MemberStatsWidget extends BaseWidget
{
    public function render(): View
    {
        return view('components.widgets.member-stats', [
            'data' => $this->getData(),
        ]);
    }

    public function getData(): array
    {
        // Scope by community if user is Director
        $query = Member::query();
        
        if ($this->user->hasRole(\App\Enums\UserRole::DIRECTOR)) {
             $query->where('community_id', $this->user->community_id);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(), // Assuming 'status' column
            'in_formation' => $query->whereHas('formationEvents')->count(), // Simplified logic
        ];
    }
}
