<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Community;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_belongs_to_community()
    {
        $community = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community->id]);

        $this->assertInstanceOf(Community::class, $member->community);
        $this->assertEquals($community->id, $member->community->id);
    }

    public function test_member_has_required_fields()
    {
        $member = Member::factory()->create([
            'first_name' => 'Mary',
            'last_name' => 'Smith',
            'religious_name' => 'Sister Mary',
            'status' => 'active',
        ]);

        $this->assertEquals('Sister Mary', $member->religious_name);
        $this->assertEquals('Mary', $member->first_name);
        $this->assertEquals('Smith', $member->last_name);
        $this->assertEquals('active', $member->status);
    }

    public function test_member_dates_are_casted()
    {
        $member = Member::factory()->create([
            'dob' => '1980-01-01',
            'entry_date' => '2000-01-01',
        ]);

        $this->assertInstanceOf(Carbon::class, $member->dob);
        $this->assertInstanceOf(Carbon::class, $member->entry_date);
        $this->assertEquals('1980-01-01', $member->dob->format('Y-m-d'));
    }

    public function test_member_uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(Member::class));
    }
}
