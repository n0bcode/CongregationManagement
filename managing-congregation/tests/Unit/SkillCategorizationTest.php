<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\SkillCategory;
use App\Enums\SkillProficiency;
use App\Models\Community;
use App\Models\Member;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: congregation-management-mvp, Property 4: Skills Are Categorized Correctly
 * Validates: Requirements 2.3
 *
 * For any skill entry, the category should be one of (pastoral, practical, special)
 * and proficiency should be a valid level
 */
class SkillCategorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    /**
     * @test
     */
    public function skill_must_have_valid_category(): void
    {
        $community = Community::factory()->create();
        $member = Member::withoutGlobalScopes()->create([
            'community_id' => $community->id,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'dob' => now()->subYears(30),
            'entry_date' => now()->subYears(5),
            'status' => 'Active',
        ]);

        $validCategories = [SkillCategory::Pastoral, SkillCategory::Practical, SkillCategory::Special];

        foreach ($validCategories as $category) {
            $skill = Skill::create([
                'member_id' => $member->id,
                'category' => $category,
                'name' => 'Test Skill',
                'proficiency' => SkillProficiency::Intermediate,
            ]);

            $this->assertInstanceOf(SkillCategory::class, $skill->category);
            $this->assertEquals($category, $skill->category);
        }
    }

    /**
     * @test
     */
    public function skill_must_have_valid_proficiency_level(): void
    {
        $community = Community::factory()->create();
        $member = Member::withoutGlobalScopes()->create([
            'community_id' => $community->id,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'dob' => now()->subYears(30),
            'entry_date' => now()->subYears(5),
            'status' => 'Active',
        ]);

        $validProficiencies = [
            SkillProficiency::Beginner,
            SkillProficiency::Intermediate,
            SkillProficiency::Advanced,
            SkillProficiency::Expert,
        ];

        foreach ($validProficiencies as $proficiency) {
            $skill = Skill::create([
                'member_id' => $member->id,
                'category' => SkillCategory::Pastoral,
                'name' => 'Test Skill',
                'proficiency' => $proficiency,
            ]);

            $this->assertInstanceOf(SkillProficiency::class, $skill->proficiency);
            $this->assertEquals($proficiency, $skill->proficiency);
        }
    }

    /**
     * @test
     */
    public function skills_are_grouped_by_category(): void
    {
        $community = Community::factory()->create();
        $member = Member::withoutGlobalScopes()->create([
            'community_id' => $community->id,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'dob' => now()->subYears(30),
            'entry_date' => now()->subYears(5),
            'status' => 'Active',
        ]);

        // Create skills in different categories
        Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Pastoral,
            'name' => 'Teaching',
            'proficiency' => SkillProficiency::Advanced,
        ]);

        Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Practical,
            'name' => 'Cooking',
            'proficiency' => SkillProficiency::Expert,
        ]);

        Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Special,
            'name' => 'Music',
            'proficiency' => SkillProficiency::Intermediate,
        ]);

        $skills = $member->skills;

        $this->assertEquals(3, $skills->count());

        // Verify each category is present
        $categories = $skills->pluck('category')->map(fn ($c) => $c->value)->toArray();
        $this->assertContains('pastoral', $categories);
        $this->assertContains('practical', $categories);
        $this->assertContains('special', $categories);
    }

    /**
     * @test
     */
    public function skill_category_enum_has_labels(): void
    {
        $this->assertEquals('Pastoral Ministry', SkillCategory::Pastoral->label());
        $this->assertEquals('Practical Skills', SkillCategory::Practical->label());
        $this->assertEquals('Special Abilities', SkillCategory::Special->label());
    }

    /**
     * @test
     */
    public function skill_proficiency_enum_has_labels(): void
    {
        $this->assertEquals('Beginner', SkillProficiency::Beginner->label());
        $this->assertEquals('Intermediate', SkillProficiency::Intermediate->label());
        $this->assertEquals('Advanced', SkillProficiency::Advanced->label());
        $this->assertEquals('Expert', SkillProficiency::Expert->label());
    }

    /**
     * @test
     */
    public function member_can_have_multiple_skills_in_same_category(): void
    {
        $community = Community::factory()->create();
        $member = Member::withoutGlobalScopes()->create([
            'community_id' => $community->id,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'dob' => now()->subYears(30),
            'entry_date' => now()->subYears(5),
            'status' => 'Active',
        ]);

        // Create multiple pastoral skills
        Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Pastoral,
            'name' => 'Teaching',
            'proficiency' => SkillProficiency::Advanced,
        ]);

        Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Pastoral,
            'name' => 'Counseling',
            'proficiency' => SkillProficiency::Intermediate,
        ]);

        $pastoralSkills = $member->skills()
            ->where('category', SkillCategory::Pastoral)
            ->get();

        $this->assertEquals(2, $pastoralSkills->count());

        // Skills are ordered by name (from Member model relationship)
        $skillNames = $pastoralSkills->pluck('name')->toArray();
        $this->assertContains('Teaching', $skillNames);
        $this->assertContains('Counseling', $skillNames);
    }

    /**
     * @test
     */
    public function skills_relationship_works_correctly(): void
    {
        $community = Community::factory()->create();
        $member = Member::withoutGlobalScopes()->create([
            'community_id' => $community->id,
            'first_name' => 'Test',
            'last_name' => 'Member',
            'dob' => now()->subYears(30),
            'entry_date' => now()->subYears(5),
            'status' => 'Active',
        ]);

        $skill = Skill::create([
            'member_id' => $member->id,
            'category' => SkillCategory::Practical,
            'name' => 'Carpentry',
            'proficiency' => SkillProficiency::Expert,
        ]);

        // Test forward relationship
        $retrievedMember = $skill->member()->withoutGlobalScopes()->first();
        $this->assertInstanceOf(Member::class, $retrievedMember);
        $this->assertEquals($member->id, $retrievedMember->id);

        // Test reverse relationship
        $this->assertTrue($member->skills->contains($skill));
    }
}
