<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Community;
use App\Models\Project;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_dashboard_renders_correctly()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        $this->actingAs($user)
            ->get('/financials/dashboard')
            ->assertStatus(200)
            ->assertSeeLivewire(\App\Livewire\FinancialDashboard::class);
    }

    public function test_charts_receive_data()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 10000, // $100
            'date' => now(),
            'category' => 'Utilities'
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\FinancialDashboard::class)
            ->assertSet('communityId', $community->id)
            ->assertViewHas('monthlyExpenses')
            ->assertViewHas('expensesByCategory');
    }

    public function test_budget_vs_actual_chart_loads_when_project_selected()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();
        
        $project = Project::factory()->create(['community_id' => $community->id, 'budget' => 5000]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\FinancialDashboard::class)
            ->set('projectId', $project->id)
            ->assertViewHas('budgetVsActual');
    }

    public function test_can_export_expenses()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 5000,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\FinancialDashboard::class)
            ->call('export', 'csv')
            ->assertFileDownloaded();
    }
}
