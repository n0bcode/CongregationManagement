<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_director_can_add_health_record()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($director)->post(route('members.health.store', $member), [
            'condition' => 'Hypertension',
            'medications' => 'Lisinopril',
            'notes' => 'Monitor blood pressure',
            'recorded_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('members.show', $member));
        $this->assertDatabaseHas('health_records', [
            'member_id' => $member->id,
            'condition' => 'Hypertension',
        ]);
    }

    public function test_director_cannot_add_health_record_to_other_community_member()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->post(route('members.health.store', $member), [
            'condition' => 'Hypertension',
            'recorded_at' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(404);
    }

    public function test_director_can_delete_health_record()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        
        // Create a health record manually or via factory if available
        // Assuming HealthRecord model has factory, if not create manually
        $healthRecord = $member->healthRecords()->create([
            'condition' => 'Flu',
            'recorded_at' => now(),
            'recorded_by' => $director->id,
        ]);

        $response = $this->actingAs($director)->delete(route('health-records.destroy', $healthRecord));

        $response->assertRedirect(route('members.show', $member));
        $this->assertDatabaseMissing('health_records', ['id' => $healthRecord->id]);
    }
}
