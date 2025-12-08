<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Project;
use App\Services\ChartService;
use Livewire\Component;

class FinancialDashboard extends Component
{
    public $year;
    public $communityId;
    public $projectId;

    public function mount()
    {
        $this->year = now()->year;
        $this->communityId = auth()->user()->community_id;
    }

    public function render(ChartService $chartService)
    {
        $user = auth()->user();
        
        // Scope communities based on role
        $communities = $user->hasRole(UserRole::DIRECTOR) 
            ? Community::where('id', $user->community_id)->get()
            : Community::orderBy('name')->get();

        // If no community selected (and not restricted), default to first
        if (!$this->communityId && $communities->isNotEmpty()) {
            $this->communityId = $communities->first()->id;
        }

        $projects = Project::where('community_id', $this->communityId)->get();

        $monthlyExpenses = $chartService->getMonthlyExpenses($this->communityId, $this->year);
        $expensesByCategory = $chartService->getExpensesByCategory($this->communityId, $this->year);
        
        $budgetVsActual = [];
        if ($this->projectId) {
            $budgetVsActual = $chartService->getBudgetVsActual($this->projectId);
        }

        return view('livewire.financial-dashboard', [
            'communities' => $communities,
            'projects' => $projects,
            'monthlyExpenses' => $monthlyExpenses,
            'expensesByCategory' => $expensesByCategory,
            'budgetVsActual' => $budgetVsActual,
        ])->layout('layouts.app');
    }

    public function updatedCommunityId()
    {
        $this->projectId = null; // Reset project when community changes
    }

    public function export(string $format)
    {
        $filters = [
            'community_id' => $this->communityId,
            'year' => $this->year,
            'project_id' => $this->projectId,
        ];

        return app(\App\Services\FinancialReportService::class)->exportExpenses($filters, $format);
    }
}
