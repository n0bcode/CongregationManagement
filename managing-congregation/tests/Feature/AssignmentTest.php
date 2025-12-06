<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Community;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_can_have_type()
    {
        $community = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $assignment = Assignment::create([
            'member_id' => $member->id,
            'community_id' => $community->id,
            'start_date' => now(),
            'role' => 'Teacher',
            'type' => 'ministry',
        ]);

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'type' => 'ministry',
        ]);
    }
}
