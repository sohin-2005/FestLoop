<?php

use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Notifications\EventReminder;
use Illuminate\Support\Facades\Notification;

function reminderEvent(array $overrides = []): Event
{
    $club = Club::factory()->create(['status' => 'approved', 'approved_at' => now()]);

    return Event::factory()->create(array_merge([
        'club_id'    => $club->id,
        'start_time' => now()->addHours(12),
        'end_time'   => now()->addHours(14),
    ], $overrides));
}

test('reminders go out to everyone holding a spot', function () {
    Notification::fake();

    $event = reminderEvent();
    $registered = User::factory()->create();
    $pending = User::factory()->create();
    $waitlisted = User::factory()->create();

    $event->registrations()->create(['user_id' => $registered->id, 'status' => Registration::REGISTERED]);
    $event->registrations()->create(['user_id' => $pending->id, 'status' => Registration::PENDING]);
    $event->registrations()->create(['user_id' => $waitlisted->id, 'status' => Registration::WAITLISTED]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Notification::assertSentTo($registered, EventReminder::class);
    Notification::assertSentTo($pending, EventReminder::class);
    Notification::assertNotSentTo($waitlisted, EventReminder::class);
});

test('an event is only reminded once', function () {
    Notification::fake();

    $event = reminderEvent();
    $user = User::factory()->create();
    $event->registrations()->create(['user_id' => $user->id, 'status' => Registration::REGISTERED]);

    $this->artisan('events:send-reminders')->assertSuccessful();
    $this->artisan('events:send-reminders')->assertSuccessful();

    Notification::assertSentToTimes($user, EventReminder::class, 1);
    expect($event->fresh()->reminder_sent_at)->not->toBeNull();
});

test('events outside the reminder window are left alone', function () {
    Notification::fake();

    $event = reminderEvent(['start_time' => now()->addDays(10), 'end_time' => now()->addDays(10)->addHour()]);
    $user = User::factory()->create();
    $event->registrations()->create(['user_id' => $user->id, 'status' => Registration::REGISTERED]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Notification::assertNothingSent();
    expect($event->fresh()->reminder_sent_at)->toBeNull();
});

test('a coordinator can export their event registrations as csv', function () {
    $club = Club::factory()->create(['status' => 'approved']);
    $coordinator = Coordinator::factory()->create(['club_id' => $club->id]);
    $event = Event::factory()->create(['club_id' => $club->id, 'start_time' => now()->addDays(2)]);

    $student = User::factory()->create(['name' => 'Asha Menon', 'email' => 'asha@example.com']);
    $event->registrations()->create(['user_id' => $student->id, 'status' => Registration::REGISTERED]);

    $response = $this->actingAs($coordinator, 'coordinator')
        ->get(route('coordinator.events.registrations.export', $event));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();
    expect($csv)->toContain('Name,Email')
        ->and($csv)->toContain('Asha Menon')
        ->and($csv)->toContain('asha@example.com');
});

test('a coordinator cannot export another club\'s registrations', function () {
    $event = reminderEvent();
    $otherClub = Club::factory()->create(['status' => 'approved']);
    $otherCoordinator = Coordinator::factory()->create(['club_id' => $otherClub->id]);

    $this->actingAs($otherCoordinator, 'coordinator')
        ->get(route('coordinator.events.registrations.export', $event))
        ->assertForbidden();
});
