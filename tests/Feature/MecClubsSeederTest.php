<?php

use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use Database\Seeders\MecClubsSeeder;

test('seeds all 22 MEC clubs as approved', function () {
    $this->seed(MecClubsSeeder::class);

    expect(Club::count())->toBe(22)
        ->and(Club::approved()->count())->toBe(22)
        ->and(Coordinator::count())->toBe(22);
});

test('seeding twice does not duplicate anything', function () {
    $this->seed(MecClubsSeeder::class);
    $this->seed(MecClubsSeeder::class);

    expect(Club::count())->toBe(22)
        ->and(Coordinator::count())->toBe(22)
        ->and(Event::count())->toBe(2);
});

test('real club pages render', function () {
    $this->seed(MecClubsSeeder::class);

    $this->get(route('clubs.show', Club::where('short_name', 'ILU')->first()))
        ->assertOk()
        ->assertSee('The Illuminati Quiz');

    $this->get(route('clubs.index'))->assertOk()->assertSee('FOSS MEC');
});

test('seeded club data contains no phone numbers', function () {
    $this->seed(MecClubsSeeder::class);

    $blob = Club::with(['announcements', 'achievements', 'events'])->get()->toJson();

    // Indian mobile numbers, with or without a +91 prefix.
    // Club sites list treasurer contact details; none of that belongs here.
    expect(preg_match('/(\+91[\s-]?)?[6-9]\d{9}\b/', $blob))->toBe(0);
});
