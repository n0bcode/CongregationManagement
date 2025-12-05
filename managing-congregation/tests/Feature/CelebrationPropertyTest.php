<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Services\CelebrationCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CelebrationPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 33: Celebration Card Generation Performance
     * Validates: Requirements 11.3
     */
    public function test_card_generation_performance()
    {
        $service = new CelebrationCardService;
        $member = Member::factory()->make([
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        $startTime = microtime(true);

        for ($i = 0; $i < 50; $i++) {
            $service->generateBirthdayCard($member);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $averageTime = $duration / 50;

        // Assert average generation time is under 200ms
        $this->assertLessThan(0.2, $averageTime, "Card generation took too long: {$averageTime}s average");
    }

    /**
     * Property 34: Upcoming Celebrations Filter By Date
     * Validates: Requirements 11.5
     */
    public function test_upcoming_celebrations_filter()
    {
        // Create members with birthdays today, in 15 days, in 29 days, and in 31 days
        $today = now();

        $memberToday = Member::factory()->create(['dob' => $today->copy()->subYears(20)]);
        $member15Days = Member::factory()->create(['dob' => $today->copy()->addDays(15)->subYears(25)]);
        $member29Days = Member::factory()->create(['dob' => $today->copy()->addDays(29)->subYears(30)]);
        $member31Days = Member::factory()->create(['dob' => $today->copy()->addDays(31)->subYears(35)]);

        // Query logic using the new scope with fixed date
        $upcomingBirthdays = Member::withoutGlobalScopes()->upcomingBirthdays($today)->pluck('id');

        $this->assertContains($memberToday->id, $upcomingBirthdays);
        $this->assertContains($member15Days->id, $upcomingBirthdays);
        $this->assertContains($member29Days->id, $upcomingBirthdays);
        $this->assertNotContains($member31Days->id, $upcomingBirthdays);
    }
}
