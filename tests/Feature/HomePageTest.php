<?php

use App\Models\Club;
use App\Models\Event;
use Database\Seeders\MecClubsSeeder;

test('the landing page is the front door, not the events list', function () {
    $this->seed(MecClubsSeeder::class);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertViewIs('home');
    $response->assertSee('Every event');
});

test('the events browser moved to /events and still lists events', function () {
    $club = Club::factory()->create(['status' => 'approved', 'approved_at' => now()]);
    Event::factory()->create([
        'club_id'    => $club->id,
        'name'       => 'Findable Event',
        'start_time' => now()->addDays(4),
    ]);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSee('Findable Event');

    expect(route('events.index', absolute: false))->toBe('/events');
});

test('the landing page shows real counts, never a zero tile', function () {
    $this->seed(MecClubsSeeder::class);

    $response = $this->get('/');

    // 22 approved clubs are seeded, so that count must be on the page.
    $response->assertSee((string) Club::approved()->count());

    // No registrations exist in the real-club seed, so that tile should be
    // dropped rather than rendered as 0. Assert on the counter markup, not the
    // word "Sign-ups" — that also appears in the "for clubs" perks list.
    $response->assertDontSee('data-to="0"', escape: false);
});

test('the hero logo wall uses real club logos', function () {
    $this->seed(MecClubsSeeder::class);

    $response = $this->get('/');

    $logo = Club::approved()->whereNotNull('logo_path')->first();

    $response->assertSee('storage/'.$logo->logo_path, escape: false);
});

test('the landing page still works with an empty database', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Nothing scheduled yet');
});
