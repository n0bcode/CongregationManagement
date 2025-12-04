<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule reminders
Schedule::command('reminders:send --schedule')->daily()->at('06:00');
Schedule::command('reminders:send')->daily()->at('08:00');

// Schedule backups
Schedule::command('system:backup')->daily()->at('02:00');
