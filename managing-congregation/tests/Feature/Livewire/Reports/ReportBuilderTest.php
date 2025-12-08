<?php

namespace Tests\Feature\Livewire\Reports;

use App\Livewire\Reports\ReportBuilder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReportBuilder::class)
            ->assertStatus(200);
    }

    public function test_can_switch_source()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReportBuilder::class)
            ->set('source', 'financials')
            ->assertSet('source', 'financials')
            ->assertSet('availableFields', [
                'amount' => 'Amount',
                'category' => 'Category',
                'description' => 'Description',
                'date' => 'Date',
            ]);
    }

    public function test_can_add_filter()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReportBuilder::class)
            ->call('addFilter', 'status', 'active')
            ->assertSet('filters', ['status' => 'active']);
    }

    public function test_can_save_template()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReportBuilder::class)
            ->set('templateName', 'My Report')
            ->set('source', 'members')
            ->call('saveTemplate')
            ->assertDispatched('notify');

        $this->assertDatabaseHas('report_templates', [
            'name' => 'My Report',
            'created_by' => $user->id,
        ]);
    }
}
