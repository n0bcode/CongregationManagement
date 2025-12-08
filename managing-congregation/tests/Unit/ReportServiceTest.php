<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\ReportTemplate;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_report_from_template()
    {
        $community = \App\Models\Community::factory()->create();
        Member::factory()->create(['first_name' => 'John', 'status' => 'active', 'community_id' => $community->id]);
        Member::factory()->create(['first_name' => 'Jane', 'status' => 'exited', 'community_id' => $community->id]);

        $user = User::factory()->create(['community_id' => $community->id]);
        $template = ReportTemplate::create([
            'name' => 'Active Members',
            'config' => [
                'source' => 'members',
                'filters' => ['status' => 'active'],
                'fields' => ['first_name', 'status'],
            ],
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);
        $service = new ReportService();
        $results = $service->generate($template);

        $this->assertCount(1, $results);
        $this->assertEquals('John', $results->first()->first_name);
    }

    public function test_export_to_csv()
    {
        $data = collect([
            ['name' => 'A', 'value' => 1],
            ['name' => 'B', 'value' => 2],
        ]);

        $service = new ReportService();
        $csv = $service->export($data, 'csv');

        $this->assertStringContainsString('name,value', $csv);
        $this->assertStringContainsString('A,1', $csv);
        $this->assertStringContainsString('B,2', $csv);
    }
}
