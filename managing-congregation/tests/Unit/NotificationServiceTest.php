<?php

namespace Tests\Unit;

use App\Models\Community;
use App\Models\FormationEvent;
use App\Models\Member;
use App\Models\Reminder;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService;

        // Authenticate as Super Admin to bypass scopes
        $user = User::factory()->create([
            'id' => 1,
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
        ]);
        $this->actingAs($user);
    }

    public function test_send_birthday_notifications_creates_reminders()
    {
        $community = Community::factory()->create();

        // Member with birthday in 7 days (default setting)
        $targetDate = now()->addDays(7);
        $member = Member::factory()->create([
            'community_id' => $community->id,
            'dob' => $targetDate->copy()->subYears(25)->format('Y-m-d'), // Turning 25
        ]);

        // Member with birthday tomorrow (should be ignored)
        Member::factory()->create([
            'community_id' => $community->id,
            'dob' => now()->addDay()->subYears(30)->format('Y-m-d'),
        ]);

        $count = $this->service->sendBirthdayNotifications();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('reminders', [
            'type' => 'birthday',
            'member_id' => $member->id,
            'reminder_date' => $targetDate->toDateString(),
        ]);
    }

    public function test_schedule_vow_reminders_creates_reminders()
    {
        $community = Community::factory()->create();

        // Member with temporary vows expiring in 90 days (default setting)
        $targetDate = now()->addDays(90);
        $member = Member::factory()->create([
            'community_id' => $community->id,
        ]);

        FormationEvent::factory()->create([
            'member_id' => $member->id,
            'stage' => \App\Enums\FormationStage::FirstVows,
            'started_at' => $targetDate->format('Y-m-d'),
        ]);

        $count = $this->service->scheduleVowReminders();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('reminders', [
            'type' => 'vow_expiration',
            'member_id' => $member->id,
            'reminder_date' => $targetDate->toDateString(),
        ]);
    }

    public function test_get_upcoming_reminders_returns_correct_reminders()
    {
        $community = Community::factory()->create();

        // Upcoming reminder
        Reminder::factory()->create([
            'community_id' => $community->id,
            'reminder_date' => now()->addDays(5),
            'is_sent' => false,
        ]);

        // Past reminder (overdue)
        Reminder::factory()->create([
            'community_id' => $community->id,
            'reminder_date' => now()->subDay(),
            'is_sent' => false,
        ]);

        // Future reminder (outside range)
        Reminder::factory()->create([
            'community_id' => $community->id,
            'reminder_date' => now()->addDays(40),
            'is_sent' => false,
        ]);

        // Sent reminder
        Reminder::factory()->sent()->create([
            'community_id' => $community->id,
            'reminder_date' => now()->addDays(5),
        ]);

        $reminders = $this->service->getUpcomingReminders(30, $community->id);

        $this->assertCount(1, $reminders);
    }
}
