<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_contains_livewire_component()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSeeLivewire(Dashboard::class);
    }

    public function test_dashboard_renders_widgets()
    {
        $user = User::factory()->create();
        
        // Register widgets
        $dashboardService = app(\App\Services\DashboardService::class);
        $dashboardService->registerWidget('member_stats', \App\View\Components\Widgets\MemberStatsWidget::class);
        $dashboardService->registerWidget('quick_actions', \App\View\Components\Widgets\QuickActionsWidget::class);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Total Members')
            ->assertSee('Quick Actions');
    }
}
