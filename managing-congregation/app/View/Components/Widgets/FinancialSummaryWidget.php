<?php

namespace App\View\Components\Widgets;

use App\Models\Expense; // Assuming Expense model exists
use App\Models\User;
use Illuminate\Contracts\View\View;

class FinancialSummaryWidget extends BaseWidget
{
    public function render(): View
    {
        return view('components.widgets.financial-summary', [
            'data' => $this->getData(),
        ]);
    }

    public function getData(): array
    {
        $query = Expense::query();

        if ($this->user->hasRole(\App\Enums\UserRole::DIRECTOR)) {
            $query->where('community_id', $this->user->community_id);
        }

        // Placeholder logic for financial summary
        return [
            'total_expenses' => $query->sum('amount'), // Assuming 'amount' column
            'recent_expenses' => $query->latest()->take(5)->get(),
        ];
    }

    public function canView(User $user): bool
    {
        return $user->hasRole(\App\Enums\UserRole::SUPER_ADMIN) || $user->hasRole(\App\Enums\UserRole::DIRECTOR);
    }
}
