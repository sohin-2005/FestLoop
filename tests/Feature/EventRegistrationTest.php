<?php

use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;

function approvedClubEvent(array $eventOverrides = []): Event
{
    $club = Club::factory()->create(['status' => 'approved', 'approved_at' => now()]);
    $coordinator = Coordinator::factory()->create(['club_id' => $club->id]);

    return Event::factory()->create(array_merge([
        'club_id' => $club->id,
        'coordinator_id' => $coordinator->id,
        'start_time' => now()->addDays(3),
        'end_time' => now()->addDays(3)->addHours(2),
    ], $eventOverrides));
}

test('events index only shows events from approved clubs', function () {
    $visible = approvedClubEvent(['name' => 'Visible Event']);

    $pendingClub = Club::factory()->create(['status' => 'pending']);
    Event::factory()->create(['club_id' => $pendingClub->id, 'name' => 'Hidden Event', 'start_time' => now()->addDay()]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Visible Event');
    $response->assertDontSee('Hidden Event');
});

test('a student can register for an event', function () {
    $user = User::factory()->create();
    $event = approvedClubEvent();

    $response = $this->actingAs($user)->post(route('events.register', $event));

    $response->assertRedirect();
    $this->assertDatabaseHas('registrations', [
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => Registration::REGISTERED,
    ]);
});

test('registering for a full event waitlists the student', function () {
    $event = approvedClubEvent(['max_participants' => 1]);
    $first = User::factory()->create();
    $second = User::factory()->create();

    $this->actingAs($first)->post(route('events.register', $event));
    $this->actingAs($second)->post(route('events.register', $event));

    $this->assertDatabaseHas('registrations', ['user_id' => $first->id, 'status' => Registration::REGISTERED]);
    $this->assertDatabaseHas('registrations', ['user_id' => $second->id, 'status' => Registration::WAITLISTED]);
});

test('cancelling a registration promotes the next waitlisted student', function () {
    $event = approvedClubEvent(['max_participants' => 1]);
    $first = User::factory()->create();
    $second = User::factory()->create();

    $event->registerUser($first);
    $event->registerUser($second);

    $event->cancelRegistration($first);

    $this->assertDatabaseMissing('registrations', ['user_id' => $first->id]);
    $this->assertDatabaseHas('registrations', ['user_id' => $second->id, 'status' => Registration::REGISTERED]);
});

test('a student can follow and unfollow a club', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create(['status' => 'approved']);

    $this->actingAs($user)->post(route('clubs.follow', $club))->assertRedirect();
    expect($club->isFollowedBy($user->fresh()))->toBeTrue();

    $this->actingAs($user)->delete(route('clubs.unfollow', $club))->assertRedirect();
    expect($club->isFollowedBy($user->fresh()))->toBeFalse();
});

test('a coordinator can publish an event for their club', function () {
    $club = Club::factory()->create(['status' => 'approved']);
    $coordinator = Coordinator::factory()->create(['club_id' => $club->id]);

    $response = $this->actingAs($coordinator, 'coordinator')->post(route('coordinator.events.store'), [
        'name' => 'Robotics Bootcamp',
        'description' => 'Hands-on robotics for beginners.',
        'category' => 'workshop',
        'mode' => 'offline',
        'location' => 'Lab 3',
        'start_time' => now()->addWeek()->format('Y-m-d H:i:s'),
    ]);

    $response->assertRedirect(route('coordinator.events.index'));
    $this->assertDatabaseHas('events', ['name' => 'Robotics Bootcamp', 'club_id' => $club->id]);
});

test('a coordinator cannot edit another club\'s event', function () {
    $event = approvedClubEvent();
    $otherClub = Club::factory()->create(['status' => 'approved']);
    $otherCoordinator = Coordinator::factory()->create(['club_id' => $otherClub->id]);

    $this->actingAs($otherCoordinator, 'coordinator')
        ->get(route('coordinator.events.edit', $event))
        ->assertForbidden();
});
