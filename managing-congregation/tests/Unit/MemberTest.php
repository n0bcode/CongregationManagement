<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Member;
use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
            'name' => 'Sister Mary',
            'civil_name' => 'Mary Smith',
            'status' => 'Active',
        ]);

        $this->assertEquals('Sister Mary', $member->name);
        $this->assertEquals('Mary Smith', $member->civil_name);
        $this->assertEquals('Active', $member->status);
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
