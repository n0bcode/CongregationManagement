<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send
                            {--schedule : Schedule new reminders instead of sending existing ones}
                            {--all : Process all reminder types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due reminders or schedule new reminders';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        if ($this->option('schedule') || $this->option('all')) {
            $this->info('Scheduling new reminders...');

            // Schedule vow reminders
            $vowCount = $notificationService->scheduleVowReminders();
            $this->info("✓ Scheduled {$vowCount} vow expiration reminders");

            // Schedule birthday reminders
            $birthdayCount = $notificationService->sendBirthdayNotifications();
            $this->info("✓ Scheduled {$birthdayCount} birthday reminders");

            // Schedule health check reminders
            $healthCount = $notificationService->scheduleHealthCheckReminders();
            $this->info("✓ Scheduled {$healthCount} health check reminders");

            $totalScheduled = $vowCount + $birthdayCount + $healthCount;
            $this->info("Total reminders scheduled: {$totalScheduled}");
        }

        if (! $this->option('schedule') || $this->option('all')) {
            $this->info('Processing due reminders...');

            // Process due reminders
            $processedCount = $notificationService->processDueReminders();
            $this->info("✓ Processed {$processedCount} due reminders");
        }

        $this->info('✓ Reminder processing complete!');

        return Command::SUCCESS;
    }
}
