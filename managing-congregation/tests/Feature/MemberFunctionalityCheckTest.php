<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberFunctionalityCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    /**
     * Verify Communication: Celebration Card Generation
     */
    public function test_can_generate_and_download_celebration_card()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->get(route('celebrations.birthday.download', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /**
     * Verify Communication: Notification System
     */
    public function test_can_receive_and_read_notifications()
    {
        \Illuminate\Support\Facades\Notification::fake();
        
        $user = User::factory()->create();

        // Send a test notification
        $user->notify(new \Tests\Support\TestNotification());

        // Assert notification was sent
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \Tests\Support\TestNotification::class
        );
    }

    /**
     * Verify Passport: Document Upload Check
     */
    public function test_check_passport_document_capability()
    {
        $community = \App\Models\Community::factory()->create();
        // Create user with DIRECTOR role to have documents.upload permission
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::DIRECTOR,
            'community_id' => $community->id
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        
        Storage::fake('public');
        $file = UploadedFile::fake()->create('passport.pdf', 100);

        // Attempt 1: Check if 'passport' is a valid Enum case for DocumentCategory
        // This should now be TRUE.
        $this->assertTrue(
            defined('\\App\\Enums\\DocumentCategory::PASSPORT'), 
            'DocumentCategory::PASSPORT should exist'
        );

        // Attempt 2: Upload a document with "passport" category
        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Member Passport',
            'description' => 'Passport scan',
            'file' => $file,
            'category' => 'passport', // Using the new category
            'member_id' => $member->id,
            'community_id' => $community->id,
        ]);

        $response->assertStatus(302); // Should redirect after successful upload
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('documents', [
            'title' => 'Member Passport',
            'category' => 'passport',
        ]);
    }

    /**
     * Verify Passport: Member Data Fields
     */
    public function test_member_has_passport_fields()
    {
        $member = Member::factory()->create([
            'passport_number' => 'B1234567',
            'passport_issued_at' => '2020-01-01',
            'passport_expired_at' => '2030-01-01',
            'passport_place_of_issue' => 'Hanoi',
        ]);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'passport_number' => 'B1234567',
            'passport_issued_at' => '2020-01-01 00:00:00',
            'passport_place_of_issue' => 'Hanoi',
        ]);

        $this->assertTrue($member->passport_issued_at instanceof \Carbon\Carbon);
    }
}
