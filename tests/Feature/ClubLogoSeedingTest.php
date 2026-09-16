<?php

use App\Models\Club;
use Database\Seeders\MecClubsSeeder;
use Illuminate\Support\Facades\Storage;

test('club logos are copied onto the public disk and resolve to urls', function () {
    Storage::fake('public');

    $this->seed(MecClubsSeeder::class);

    $withLogos = Club::whereNotNull('logo_path')->get();

    // 21 of the 22 clubs publish a logo (Fortitude does not).
    expect($withLogos)->toHaveCount(21);

    foreach ($withLogos as $club) {
        Storage::disk('public')->assertExists($club->logo_path);
        expect($club->logo_url)->toContain('storage/'.$club->logo_path);
    }
});

test('a club without a published logo still seeds and renders', function () {
    Storage::fake('public');

    $this->seed(MecClubsSeeder::class);

    $fortitude = Club::where('short_name', 'FRT')->firstOrFail();

    expect($fortitude->logo_path)->toBeNull()
        ->and($fortitude->logo_url)->toBeNull()
        ->and($fortitude->initials)->toBe('FRT'); // falls back to the initials badge

    $this->get(route('clubs.show', $fortitude))->assertOk()->assertSee('Fortitude');
});

test('every shipped logo file is referenced by a club', function () {
    Storage::fake('public');
    $this->seed(MecClubsSeeder::class);

    $referenced = Club::whereNotNull('logo_path')->pluck('logo_path')
        ->map(fn ($p) => basename($p))->sort()->values()->all();

    $shipped = collect(glob(database_path('seeders/assets/club-logos/*')))
        ->map(fn ($p) => basename($p))->sort()->values()->all();

    expect($referenced)->toBe($shipped);
});
