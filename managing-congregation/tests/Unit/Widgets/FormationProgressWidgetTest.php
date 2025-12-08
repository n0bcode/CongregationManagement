<?php

namespace Tests\Unit\Widgets;

use App\Models\FormationEvent;
use App\Models\Member;
use App\View\Components\Widgets\FormationProgressWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationProgressWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_data_returns_correct_counts()
    {
        // Create members with formation events
        $member1 = Member::factory()->create();
        FormationEvent::factory()->create([
            'member_id' => $member1->id,
            'stage' => 'postulancy',
            'started_at' => now()->subMonths(6),
        ]);

        $member2 = Member::factory()->create();
        FormationEvent::factory()->create([
            'member_id' => $member2->id,
            'stage' => 'novitiate',
            'started_at' => now()->subMonths(3),
        ]);

        $member3 = Member::factory()->create();
        FormationEvent::factory()->create([
            'member_id' => $member3->id,
            'stage' => 'novitiate',
            'started_at' => now()->subMonths(2),
        ]);

        // Member 4 has two events, should only count the latest (novitiate)
        $member4 = Member::factory()->create();
        FormationEvent::factory()->create([
            'member_id' => $member4->id,
            'stage' => 'postulancy',
            'started_at' => now()->subMonths(12),
        ]);
        FormationEvent::factory()->create([
            'member_id' => $member4->id,
            'stage' => 'novitiate',
            'started_at' => now()->subMonths(1),
        ]);

        $widget = new FormationProgressWidget();
        $data = $widget->getData();

        // member1: postulancy
        // member2: novitiate
        // member3: novitiate
        // member4: novitiate
        
        $this->assertEquals(3, $data['stages']['novitiate']);
        $this->assertEquals(1, $data['stages']['postulancy']);
        $this->assertEquals(4, $data['total_in_formation']);
    }
}
