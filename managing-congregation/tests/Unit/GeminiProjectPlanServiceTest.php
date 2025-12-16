<?php

namespace Tests\Unit;

use App\Services\GeminiProjectPlanService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiProjectPlanServiceTest extends TestCase
{
    public function test_it_generates_plan_successfully()
    {
        // Mock the Gemini API response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'project_name' => 'Test Project',
                                        'description' => 'Test Description',
                                        'tasks' => [
                                            [
                                                'title' => 'Test Task',
                                                'type' => 'task',
                                                'priority' => 'high'
                                            ]
                                        ]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Mock config/env
        config(['services.gemini.key' => 'fake-key']);

        $service = new GeminiProjectPlanService();
        $plan = $service->generatePlan('Build a test app');

        $this->assertIsArray($plan);
        $this->assertEquals('Test Project', $plan['project_name']);
        $this->assertCount(1, $plan['tasks']);
        $this->assertEquals('Test Task', $plan['tasks'][0]['title']);
    }

    public function test_it_uses_manual_api_key_when_config_is_empty()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[ 'content' => [ 'parts' => [[ 'text' => json_encode(['project_name' => 'Manual Key Project', 'tasks' => []]) ]] ] ]]
            ], 200)
        ]);

        // Ensure config is empty
        config(['services.gemini.key' => null]);

        $service = new GeminiProjectPlanService();
        $plan = $service->generatePlan('Test with manual key', 'manual-key-123');

        $this->assertEquals('Manual Key Project', $plan['project_name']);
        
        // Assert the request was made with the manual key
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'key=manual-key-123');
        });
    }

    public function test_it_handles_api_failure()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Error', 500)
        ]);
        
        config(['services.gemini.key' => 'fake-key']);

        $this->expectException(\Exception::class);

        $service = new GeminiProjectPlanService();
        $service->generatePlan('Build a test app');
    }
}
