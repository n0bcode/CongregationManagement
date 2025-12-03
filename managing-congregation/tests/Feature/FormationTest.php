<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\FormationStage;
use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_add_formation_event(): void
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->post(route('members.formation.store', $member), [
                'stage' => FormationStage::Postulancy->value,
                'started_at' => '2024-01-01',
                'notes' => 'Test notes',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('formation_events', [
            'member_id' => $member->id,
            'stage' => FormationStage::Postulancy->value,
            'started_at' => '2024-01-01',
            'notes' => 'Test notes',
        ]);
    }

    public function test_unauthorized_user_cannot_add_formation_event(): void
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => UserRole::MEMBER, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->post(route('members.formation.store', $member), [
                'stage' => FormationStage::Postulancy->value,
                'started_at' => '2024-01-01',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('formation_events', 0);
    }

    public function test_validation_errors(): void
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->post(route('members.formation.store', $member), [
                'stage' => 'invalid-stage',
                'started_at' => 'not-a-date',
            ]);

        $response->assertSessionHasErrors(['stage', 'started_at']);
    }

    public function test_timeline_is_rendered(): void
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        \App\Models\FormationEvent::create([
            'member_id' => $member->id,
            'stage' => FormationStage::Postulancy,
            'started_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('members.show', $member));

        $response->assertOk();
        $response->assertSee('Formation Timeline');
        $response->assertSee('Postulancy');
        $response->assertSee('Add Milestone');
    }
}
