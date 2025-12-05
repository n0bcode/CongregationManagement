<?php

namespace App\Services;

use App\Models\FormationEvent;
use App\Models\Member;
use App\Models\Reminder;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Schedule vow expiration reminders for members
     */
    public function scheduleVowReminders(): int
    {
        $reminderDays = SystemSetting::get('vow_reminder_days', 90);
        $targetDate = now()->addDays($reminderDays)->toDateString();
        $count = 0;

        // Get members with temporary vows expiring on target date
        $members = Member::whereHas('formationEvents', function ($query) use ($targetDate) {
            $query->where('stage', \App\Enums\FormationStage::FirstVows)
                ->whereDate('started_at', $targetDate);
        })->get();

        foreach ($members as $member) {
            // Check if reminder already exists
            $exists = Reminder::where('type', 'vow_expiration')
                ->where('member_id', $member->id)
                ->where('reminder_date', $targetDate)
                ->exists();

            if (! $exists) {
                Reminder::create([
                    'type' => 'vow_expiration',
                    'title' => "Vow Expiration: {$member->first_name} {$member->last_name}",
                    'description' => "Temporary vows for {$member->first_name} {$member->last_name} will expire in {$reminderDays} days.",
                    'reminder_date' => $targetDate,
                    'member_id' => $member->id,
                    'community_id' => $member->community_id,
                    'created_by' => 1, // System user
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Send birthday notifications for members with birthdays today
     */
    public function sendBirthdayNotifications(): int
    {
        $reminderDays = SystemSetting::get('birthday_reminder_days', 7);
        $targetDate = now()->addDays($reminderDays);
        $count = 0;

        // Get members with birthdays on target date (month and day match)
        $members = Member::whereMonth('dob', $targetDate->month)
            ->whereDay('dob', $targetDate->day)
            ->get();

        foreach ($members as $member) {
            // Check if reminder already exists for this year
            $exists = Reminder::where('type', 'birthday')
                ->where('member_id', $member->id)
                ->whereYear('reminder_date', $targetDate->year)
                ->exists();

            if (! $exists) {
                $age = $targetDate->year - $member->dob->year;

                Reminder::create([
                    'type' => 'birthday',
                    'title' => "Birthday: {$member->first_name} {$member->last_name}",
                    'description' => "{$member->first_name} {$member->last_name} will turn {$age} years old in {$reminderDays} days.",
                    'reminder_date' => $targetDate->toDateString(),
                    'member_id' => $member->id,
                    'community_id' => $member->community_id,
                    'created_by' => 1, // System user
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Notify Formation Directress about formation milestones
     */
    public function notifyFormationDirectress(FormationEvent $event): void
    {
        // Get Formation Directress users
        $directresses = User::where('role', 'formation_directress')
            ->orWhere('role', 'super_admin')
            ->get();

        if ($directresses->isEmpty()) {
            Log::warning('No Formation Directress users found to notify about formation event', [
                'event_id' => $event->id,
                'member_id' => $event->member_id,
            ]);

            return;
        }

        /** @var \App\Models\Member $member */
        $member = $event->member;

        // Create reminder for Formation Directress
        Reminder::create([
            'type' => 'formation_milestone',
            'title' => "Formation Milestone: {$member->first_name} {$member->last_name}",
            'description' => "Formation event '{$event->stage->value}' recorded for {$member->first_name} {$member->last_name} on {$event->started_at->format('M d, Y')}.",
            'reminder_date' => now()->toDateString(),
            'member_id' => $event->member_id,
            'community_id' => $member->community_id,
            'created_by' => 1, // System user
        ]);

        Log::info('Formation Directress notified about formation milestone', [
            'event_id' => $event->id,
            'member_id' => $event->member_id,
            'stage' => $event->stage,
        ]);
    }

    /**
     * Schedule health check reminders
     */
    public function scheduleHealthCheckReminders(): int
    {
        $reminderMonths = SystemSetting::get('health_check_reminder_months', 12);
        $count = 0;

        // Get all active members
        $members = Member::where('status', 'active')->get();

        foreach ($members as $member) {
            // Get last health record
            /** @var \App\Models\HealthRecord|null $lastHealthRecord */
            $lastHealthRecord = $member->healthRecords()->latest('recorded_at')->first();

            if ($lastHealthRecord) {
                $nextCheckDate = Carbon::parse($lastHealthRecord->recorded_at)
                    ->addMonths($reminderMonths);

                // If next check is within 30 days and no reminder exists
                if ($nextCheckDate->diffInDays(now()) <= 30 && $nextCheckDate->isFuture()) {
                    $exists = Reminder::where('type', 'health_check')
                        ->where('member_id', $member->id)
                        ->where('reminder_date', $nextCheckDate->toDateString())
                        ->exists();

                    if (! $exists) {
                        Reminder::create([
                            'type' => 'health_check',
                            'title' => "Health Check Due: {$member->first_name} {$member->last_name}",
                            'description' => "Annual health check is due for {$member->first_name} {$member->last_name}.",
                            'reminder_date' => $nextCheckDate->toDateString(),
                            'member_id' => $member->id,
                            'community_id' => $member->community_id,
                            'created_by' => 1, // System user
                        ]);

                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Get upcoming reminders for dashboard
     */
    public function getUpcomingReminders(int $days = 30, ?int $communityId = null): Collection
    {
        return Reminder::with(['member', 'community'])
            ->pending()
            ->upcoming($days)
            ->forCommunity($communityId)
            ->orderBy('reminder_date')
            ->get();
    }

    /**
     * Get overdue reminders
     */
    public function getOverdueReminders(?int $communityId = null): Collection
    {
        return Reminder::with(['member', 'community'])
            ->pending()
            ->where('reminder_date', '<', now()->toDateString())
            ->forCommunity($communityId)
            ->orderBy('reminder_date')
            ->get();
    }

    /**
     * Process due reminders (mark as sent and log)
     */
    public function processDueReminders(): int
    {
        $reminders = Reminder::pending()
            ->due()
            ->get();

        $count = 0;

        foreach ($reminders as $reminder) {
            // In a real application, you would send actual notifications here
            // For now, we just mark them as sent and log

            $reminder->markAsSent();

            Log::info('Reminder processed', [
                'reminder_id' => $reminder->id,
                'type' => $reminder->type,
                'member_id' => $reminder->member_id,
                'title' => $reminder->title,
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Create custom reminder
     */
    public function createReminder(
        string $type,
        string $title,
        string $reminderDate,
        ?string $description = null,
        ?int $memberId = null,
        ?int $communityId = null,
        ?int $createdBy = null
    ): Reminder {
        return Reminder::create([
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'reminder_date' => $reminderDate,
            'member_id' => $memberId,
            'community_id' => $communityId,
            'created_by' => $createdBy ?? auth()->id() ?? 1,
        ]);
    }
}
