<?php

use App\Models\Event;
use Database\Seeders\MecClubsSeeder;
use Illuminate\Support\Facades\Storage;

test('seeded events carry the organising club\'s own poster', function () {
    Storage::fake('public');

    $this->seed(MecClubsSeeder::class);

    $events = Event::all();

    expect($events)->toHaveCount(2);

    foreach ($events as $event) {
        expect($event->banner_image)->not->toBeNull("{$event->name} has no banner");
        Storage::disk('public')->assertExists($event->banner_image);
        expect($event->banner_url)->toContain('storage/'.$event->banner_image);
    }
});

test('event banners render on the event page', function () {
    $this->seed(MecClubsSeeder::class);

    $event = Event::where('name', 'like', '%Illuminati Quiz%')->firstOrFail();

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('event-banners/tiq.png', escape: false);
});

test('every shipped event banner is referenced by an event', function () {
    Storage::fake('public');
    $this->seed(MecClubsSeeder::class);

    $referenced = Event::whereNotNull('banner_image')->pluck('banner_image')
        ->map(fn ($p) => basename($p))->sort()->values()->all();

    $shipped = collect(glob(database_path('seeders/assets/event-banners/*')))
        ->map(fn ($p) => basename($p))->sort()->values()->all();

    expect($referenced)->toBe($shipped);
});
