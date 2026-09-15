<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Registration;
use App\Notifications\EventReminder;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders {--hours=24 : How far ahead to look}';

    protected $description = 'Send a reminder to everyone holding a spot at an event starting soon';

    public function handle(): int
    {
        $window = now()->addHours((int) $this->option('hours'));

        $events = Event::query()
            ->visible()
            ->whereNull('reminder_sent_at')
            ->whereBetween('start_time', [now(), $window])
            ->with('club')
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events need reminders right now.');

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($events as $event) {
            $registrations = $event->registrations()
                ->whereIn('status', Registration::HOLDS_SEAT)
                ->with('user')
                ->get();

            foreach ($registrations as $registration) {
                $registration->user?->notify(new EventReminder($event));
                $sent++;
            }

            $event->forceFill(['reminder_sent_at' => now()])->save();

            $this->line("Reminded {$registrations->count()} attendee(s) about \"{$event->name}\".");
        }

        $this->info("Done — {$sent} reminder(s) sent across {$events->count()} event(s).");

        return self::SUCCESS;
    }
}
