<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\Skill;
use App\Models\Ordination;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_advanced_stats()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        
        // Seed data
        $member = Member::factory()->create(['dob' => now()->subYears(25)]);
        Skill::create([
            'member_id' => $member->id,
            'category' => \App\Enums\SkillCategory::Special,
            'name' => 'Programming',
            'proficiency' => \App\Enums\SkillProficiency::Advanced,
        ]);
        Ordination::create([
            'member_id' => $member->id,
            'step' => 'priest',
            'date' => now()->subYears(5), // Anniversary is today
            'place' => 'Cathedral',
            'bishop_name' => 'Bishop John',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.advanced'));

        $response->assertStatus(200);
        $response->assertSee('Advanced Statistics');
        $response->assertSee('Programming');
        $response->assertSee('20-29');
        $response->assertSee($member->name);
    }

    public function test_non_admin_cannot_view_advanced_stats()
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($user)->get(route('reports.advanced'));

        $response->assertStatus(403);
    }
}
