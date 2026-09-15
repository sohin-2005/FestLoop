<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Run hourly so an event gets its reminder roughly 24h before it starts.
| In production, point cron at: php artisan schedule:run (every minute).
*/
Schedule::command('events:send-reminders')->hourly();
