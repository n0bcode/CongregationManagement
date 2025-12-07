<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Expense;
use App\Models\User;
use App\Services\ChartService;
use App\Services\FinancialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected FinancialService $financialService;
    protected ChartService $chartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->financialService = app(FinancialService::class);
        $this->chartService = app(ChartService::class);
    }

    /** @test */
    public function it_generates_correct_monthly_report_totals()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);

        // Create expenses
        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 1000, // $10.00
            'date' => now()->startOfMonth(),
            'category' => 'Food',
        ]);

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 2000, // $20.00
            'date' => now()->startOfMonth()->addDay(),
            'category' => 'Transport',
        ]);

        $this->actingAs($user);

        $report = $this->financialService->generateMonthlyReport(
            $community->id,
            now()->year,
            now()->month
        );

        $this->assertEquals(3000, $report['summary']['total_amount']);
        $this->assertEquals(30.00, $report['summary']['total_amount_dollars']);
        $this->assertEquals(2, $report['summary']['total_count']);
    }

    /** @test */
    public function it_aggregates_expenses_by_category_correctly()
    {
        $community = Community::factory()->create();

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 1000,
            'category' => 'Food',
            'date' => now()->startOfMonth(),
        ]);

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 1500,
            'category' => 'Food',
            'date' => now()->startOfMonth(),
        ]);

        Expense::factory()->create([
            'community_id' => $community->id,
            'amount' => 2000,
            'category' => 'Transport',
            'date' => now()->startOfMonth(),
        ]);

        $user = User::factory()->create(['community_id' => $community->id]);
        $this->actingAs($user);

        $report = $this->financialService->generateMonthlyReport(
            $community->id,
            now()->year,
            now()->month
        );

        $foodCategory = $report['by_category']->firstWhere('category', 'Food');
        $transportCategory = $report['by_category']->firstWhere('category', 'Transport');

        $this->assertEquals(2500, $foodCategory['total']);
        $this->assertEquals(2000, $transportCategory['total']);
    }

    /** @test */
    public function chart_service_prepares_correct_trend_data()
    {
        $dailyBreakdown = collect([
            [
                'date' => '2023-01-01',
                'total' => 1000,
            ],
            [
                'date' => '2023-01-02',
                'total' => 2000,
            ],
        ]);

        $data = $this->chartService->prepareExpenseTrendData($dailyBreakdown);

        $this->assertCount(2, $data['labels']);
        $this->assertEquals('Jan 01', $data['labels'][0]);
        $this->assertEquals('Jan 02', $data['labels'][1]);
        $this->assertEquals(10, $data['datasets'][0]['data'][0]); // 1000 / 100
        $this->assertEquals(20, $data['datasets'][0]['data'][1]); // 2000 / 100
    }

    /** @test */
    public function chart_service_prepares_correct_category_data()
    {
        $byCategory = collect([
            [
                'category' => 'Food',
                'total' => 5000,
            ],
            [
                'category' => 'Transport',
                'total' => 3000,
            ],
        ]);

        $data = $this->chartService->prepareCategoryDistributionData($byCategory);

        $this->assertCount(2, $data['labels']);
        $this->assertEquals('Food', $data['labels'][0]);
        $this->assertEquals(50, $data['datasets'][0]['data'][0]);
        $this->assertEquals(30, $data['datasets'][0]['data'][1]);
    }
}
