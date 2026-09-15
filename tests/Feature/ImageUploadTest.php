<?php

use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->club = Club::factory()->create(['status' => 'approved']);
    $this->coordinator = Coordinator::factory()->create(['club_id' => $this->club->id]);
});

test('a coordinator can upload a club logo and cover', function () {
    $response = $this->actingAs($this->coordinator, 'coordinator')
        ->patch(route('coordinator.club.update'), [
            'name'        => $this->club->name,
            'category'    => $this->club->category,
            'logo_image'  => UploadedFile::fake()->image('logo.png', 400, 400),
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 400),
        ]);

    $response->assertRedirect();

    $club = $this->club->fresh();

    expect($club->logo_path)->not->toBeNull()
        ->and($club->cover_path)->not->toBeNull();

    Storage::disk('public')->assertExists($club->logo_path);
    Storage::disk('public')->assertExists($club->cover_path);

    // the accessor the views rely on should produce a usable URL
    expect($club->logo_url)->toContain('storage/'.$club->logo_path);
});

test('a coordinator can upload an event banner', function () {
    $response = $this->actingAs($this->coordinator, 'coordinator')
        ->post(route('coordinator.events.store'), [
            'name'         => 'Poster Night',
            'description'  => 'Bring your best poster.',
            'category'     => 'cultural',
            'mode'         => 'offline',
            'location'     => 'Atrium',
            'start_time'   => now()->addWeek()->format('Y-m-d H:i:s'),
            'banner_image' => UploadedFile::fake()->image('banner.jpg', 1600, 900),
        ]);

    $response->assertRedirect(route('coordinator.events.index'));

    $event = Event::where('name', 'Poster Night')->firstOrFail();

    expect($event->banner_image)->not->toBeNull();
    Storage::disk('public')->assertExists($event->banner_image);
    expect($event->banner_url)->toContain('storage/'.$event->banner_image);
});

test('non-image uploads are rejected', function () {
    $this->actingAs($this->coordinator, 'coordinator')
        ->patch(route('coordinator.club.update'), [
            'name'       => $this->club->name,
            'category'   => $this->club->category,
            'logo_image' => UploadedFile::fake()->create('payload.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('logo_image');

    expect($this->club->fresh()->logo_path)->toBeNull();
});

test('an oversized image is rejected', function () {
    $this->actingAs($this->coordinator, 'coordinator')
        ->patch(route('coordinator.club.update'), [
            'name'       => $this->club->name,
            'category'   => $this->club->category,
            'logo_image' => UploadedFile::fake()->image('huge.jpg')->size(4096), // limit is 2MB
        ])
        ->assertSessionHasErrors('logo_image');
});
