<?php

namespace Tests\Unit;

use App\Models\Community;
use App\Models\Expense;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FinancialService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FinancialService;
    }

    public function test_generate_monthly_report_calculates_totals_correctly()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $this->actingAs($user);

        // Create expenses for the target month (Jan 2023)
        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 1000, // $10.00
            'date' => '2023-01-05',
            'category' => 'Food',
        ]);

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 2000, // $20.00
            'date' => '2023-01-15',
            'category' => 'Utilities',
        ]);

        // Create expense for another month (Feb 2023) - should be ignored
        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 5000,
            'date' => '2023-02-01',
        ]);

        $report = $this->service->generateMonthlyReport($community->id, 2023, 1);

        $this->assertEquals(3000, $report['summary']['total_amount']);
        $this->assertEquals(30.00, $report['summary']['total_amount_dollars']);
        $this->assertEquals(2, $report['summary']['total_count']);

        // Check category aggregation
        $this->assertCount(2, $report['by_category']);
        $this->assertEquals('Utilities', $report['by_category'][0]['category']); // Sorted by total desc
        $this->assertEquals(2000, $report['by_category'][0]['total']);
    }

    public function test_lock_period_locks_all_expenses_in_range()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $this->actingAs($user);

        $expense1 = Expense::factory()->create([
            'community_id' => $community->id,
            'date' => '2023-01-05',
            'is_locked' => false,
        ]);

        $expense2 = Expense::factory()->create([
            'community_id' => $community->id,
            'date' => '2023-01-25',
            'is_locked' => false,
        ]);

        $result = $this->service->lockPeriod($community->id, 2023, 1);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['locked_count']);

        $this->assertTrue($expense1->fresh()->is_locked);
        $this->assertTrue($expense2->fresh()->is_locked);
        $this->assertEquals($user->id, $expense1->fresh()->locked_by);
    }

    public function test_is_period_locked_returns_true_if_any_expense_locked()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $this->actingAs($user);

        // No expenses
        $this->assertFalse($this->service->isPeriodLocked($community->id, 2023, 1));

        // Unlocked expense
        Expense::factory()->create([
            'community_id' => $community->id,
            'date' => '2023-01-05',
            'is_locked' => false,
        ]);
        $this->assertFalse($this->service->isPeriodLocked($community->id, 2023, 1));

        // Locked expense
        Expense::factory()->create([
            'community_id' => $community->id,
            'date' => '2023-01-10',
            'is_locked' => true,
        ]);
        $this->assertTrue($this->service->isPeriodLocked($community->id, 2023, 1));
    }
}
