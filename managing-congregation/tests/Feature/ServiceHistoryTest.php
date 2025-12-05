<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_service_record()
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $community = Community::factory()->create();
        $user = User::factory()->director($community)->create();
        $member = Member::factory()->create(['community_id' => $community->id]);
        $targetCommunity = Community::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('members.assignments.store', $member), [
                'community_id' => $targetCommunity->id,
                'role' => 'Director',
                'start_date' => '2020-01-01',
                'end_date' => '2021-01-01',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assignments', [
            'member_id' => $member->id,
            'community_id' => $targetCommunity->id,
            'role' => 'Director',
            'start_date' => '2020-01-01',
            'end_date' => '2021-01-01',
        ]);
    }

    public function test_can_delete_service_record()
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $community = Community::factory()->create();
        $user = User::factory()->director($community)->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $assignment = Assignment::create([
            'member_id' => $member->id,
            'community_id' => $community->id,
            'start_date' => '2020-01-01',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('assignments.destroy', $assignment));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_service_record_validation()
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $community = Community::factory()->create();
        $user = User::factory()->director($community)->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->post(route('members.assignments.store', $member), [
                'community_id' => 999,
                'start_date' => 'invalid',
            ]);

        $response->assertSessionHasErrors(['community_id', 'start_date']);
    }
}
