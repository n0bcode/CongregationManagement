<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\FormationStage;
use App\Models\FormationEvent;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $fillable = (new FormationEvent())->getFillable();

        $this->assertContains('member_id', $fillable);
        $this->assertContains('stage', $fillable);
        $this->assertContains('started_at', $fillable);
        $this->assertContains('notes', $fillable);
    }

    public function test_it_casts_attributes(): void
    {
        $casts = (new FormationEvent())->getCasts();

        $this->assertEquals(FormationStage::class, $casts['stage']);
        $this->assertEquals('date', $casts['started_at']);
    }

    public function test_it_belongs_to_a_member(): void
    {
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $this->actingAs($user);

        $member = Member::factory()->create();
        $event = FormationEvent::create([
            'member_id' => $member->id,
            'stage' => FormationStage::Postulancy,
            'started_at' => now(),
            'notes' => 'Test notes',
        ]);

        $event->refresh();

        $this->assertInstanceOf(Member::class, $event->member);
        $this->assertEquals($member->id, $event->member->id);
    }
}
