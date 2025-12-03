<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\FormationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_canon_law_duration_constants(): void
    {
        $this->assertEquals(12, FormationService::NOVITIATE_MIN_MONTHS);
        $this->assertEquals(36, FormationService::FIRST_VOWS_MIN_MONTHS);
    }

    public function test_it_calculates_next_stage_date(): void
    {
        $service = new FormationService();
        $date = now();

        // Novitiate -> First Vows (+12 months)
        $expected = $date->copy()->addMonths(FormationService::NOVITIATE_MIN_MONTHS);
        $result = $service->calculateNextStageDate(\App\Enums\FormationStage::Novitiate, $date);
        $this->assertNotNull($result);
        $this->assertEquals($expected->toDateString(), $result->toDateString());

        // First Vows -> Final Vows (+36 months)
        $expected = $date->copy()->addMonths(FormationService::FIRST_VOWS_MIN_MONTHS);
        $result = $service->calculateNextStageDate(\App\Enums\FormationStage::FirstVows, $date);
        $this->assertNotNull($result);
        $this->assertEquals($expected->toDateString(), $result->toDateString());

        // Postulancy -> Variable (Null)
        $this->assertNull($service->calculateNextStageDate(\App\Enums\FormationStage::Postulancy, $date));

        // Final Vows -> End (Null)
        $this->assertNull($service->calculateNextStageDate(\App\Enums\FormationStage::FinalVows, $date));
    }

    public function test_it_adds_event(): void
    {
        $service = new FormationService();
        $member = \App\Models\Member::factory()->create();
        $data = [
            'stage' => \App\Enums\FormationStage::Postulancy,
            'started_at' => now(),
            'notes' => 'Test notes',
        ];

        $event = $service->addEvent($member, $data);

        $this->assertInstanceOf(\App\Models\FormationEvent::class, $event);
        $this->assertEquals($member->id, $event->member_id);
        $this->assertEquals(\App\Enums\FormationStage::Postulancy, $event->stage);
    }
}
