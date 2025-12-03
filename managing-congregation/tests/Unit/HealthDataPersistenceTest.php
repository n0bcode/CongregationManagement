<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Community;
use App\Models\HealthRecord;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: congregation-management-mvp, Property 3: Health Data Saves Correctly
 * Validates: Requirements 2.2
 *
 * For any valid health record data, saving it should result in a database record
 * with all fields preserved
 */
class HealthDataPersistenceTest extends TestCase
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
    public function health_record_saves_all_required_fields(): void
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
        $user = User::factory()->create(['community_id' => $community->id]);

        $healthData = [
            'member_id' => $member->id,
            'condition' => 'Hypertension',
            'medications' => 'Lisinopril 10mg daily',
            'notes' => 'Monitor blood pressure weekly',
            'recorded_at' => now()->toDateString(),
            'recorded_by' => $user->id,
        ];

        $healthRecord = HealthRecord::create($healthData);

        $this->assertDatabaseHas('health_records', [
            'id' => $healthRecord->id,
            'member_id' => $member->id,
            'condition' => 'Hypertension',
            'medications' => 'Lisinopril 10mg daily',
            'notes' => 'Monitor blood pressure weekly',
            'recorded_by' => $user->id,
        ]);

        // Verify all fields are preserved
        $retrieved = HealthRecord::find($healthRecord->id);
        $this->assertEquals($healthData['condition'], $retrieved->condition);
        $this->assertEquals($healthData['medications'], $retrieved->medications);
        $this->assertEquals($healthData['notes'], $retrieved->notes);
        $this->assertEquals($healthData['recorded_by'], $retrieved->recorded_by);
    }

    /**
     * @test
     */
    public function health_record_relationships_work_correctly(): void
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
        $user = User::factory()->create(['community_id' => $community->id]);

        $healthRecord = HealthRecord::create([
            'member_id' => $member->id,
            'condition' => 'Diabetes Type 2',
            'recorded_at' => now(),
            'recorded_by' => $user->id,
        ]);

        // Test member relationship (need to bypass global scope)
        $retrievedMember = $healthRecord->member()->withoutGlobalScopes()->first();
        $this->assertInstanceOf(Member::class, $retrievedMember);
        $this->assertEquals($member->id, $retrievedMember->id);

        // Test recorder relationship
        $this->assertInstanceOf(User::class, $healthRecord->recorder);
        $this->assertEquals($user->id, $healthRecord->recorder->id);

        // Test reverse relationship
        $this->assertTrue($member->healthRecords->contains($healthRecord));
    }

    /**
     * @test
     */
    public function multiple_health_records_can_be_stored_for_same_member(): void
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
        $user = User::factory()->create(['community_id' => $community->id]);

        $conditions = ['Hypertension', 'Diabetes', 'Arthritis'];

        foreach ($conditions as $condition) {
            HealthRecord::create([
                'member_id' => $member->id,
                'condition' => $condition,
                'recorded_at' => now(),
                'recorded_by' => $user->id,
            ]);
        }

        $this->assertEquals(3, $member->healthRecords()->count());
        $this->assertEquals($conditions, $member->healthRecords->pluck('condition')->toArray());
    }

    /**
     * @test
     */
    public function health_records_are_ordered_by_date_descending(): void
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
        $user = User::factory()->create(['community_id' => $community->id]);

        // Create records with different dates
        $oldRecord = HealthRecord::create([
            'member_id' => $member->id,
            'condition' => 'Old Condition',
            'recorded_at' => now()->subYear(),
            'recorded_by' => $user->id,
        ]);

        $newRecord = HealthRecord::create([
            'member_id' => $member->id,
            'condition' => 'New Condition',
            'recorded_at' => now(),
            'recorded_by' => $user->id,
        ]);

        $records = $member->healthRecords;

        // Most recent should be first
        $this->assertEquals($newRecord->id, $records->first()->id);
        $this->assertEquals($oldRecord->id, $records->last()->id);
    }

    /**
     * @test
     */
    public function health_record_with_document_path_saves_correctly(): void
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
        $user = User::factory()->create(['community_id' => $community->id]);

        $healthRecord = HealthRecord::create([
            'member_id' => $member->id,
            'condition' => 'Test Condition',
            'document_path' => 'health_records/test_document.pdf',
            'recorded_at' => now(),
            'recorded_by' => $user->id,
        ]);

        $this->assertDatabaseHas('health_records', [
            'id' => $healthRecord->id,
            'document_path' => 'health_records/test_document.pdf',
        ]);

        $this->assertEquals('health_records/test_document.pdf', $healthRecord->fresh()->document_path);
    }
}
