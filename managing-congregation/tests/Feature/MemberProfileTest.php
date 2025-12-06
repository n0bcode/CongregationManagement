<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_have_ordination()
    {
        $community = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $member->ordinations()->create([
            'step' => 'deacon',
            'date' => '2020-01-01',
            'place' => 'Cathedral',
            'bishop_name' => 'Bishop John',
        ]);

        $this->assertDatabaseHas('ordinations', [
            'member_id' => $member->id,
            'step' => 'deacon',
            'bishop_name' => 'Bishop John',
        ]);
        
        $this->assertEquals(1, $member->ordinations->count());
    }

    public function test_member_can_have_education()
    {
        $community = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $member->educations()->create([
            'degree' => 'Bachelor',
            'major' => 'Theology',
            'school' => 'Seminary',
            'start_year' => 2015,
            'end_year' => 2019,
            'is_graduated' => true,
        ]);

        $this->assertDatabaseHas('education', [
            'member_id' => $member->id,
            'degree' => 'Bachelor',
            'major' => 'Theology',
        ]);

        $this->assertEquals(1, $member->educations->count());
    }

    public function test_member_can_have_emergency_contacts()
    {
        $community = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $member->emergencyContacts()->create([
            'name' => 'Mom',
            'relationship' => 'Mother',
            'phone' => '1234567890',
        ]);

        $this->assertDatabaseHas('emergency_contacts', [
            'member_id' => $member->id,
            'name' => 'Mom',
            'relationship' => 'Mother',
        ]);

        $this->assertEquals(1, $member->emergencyContacts->count());
    }
}
