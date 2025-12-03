<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use App\Models\Event;

// Home page: list all events
Route::get('/', [EventController::class, 'index'])->name('events.index');

// Dashboard with stats
Route::get('/dashboard', function () {
    $totalEvents = Event::count();
    $upcomingEventsCount = Event::where('start_time', '>=', now())->count();

    $upcomingEvents = Event::where('start_time', '>=', now())
        ->orderBy('start_time')
        ->take(5)
        ->get();

    return view('dashboard', [
        'totalEvents'         => $totalEvents,
        'upcomingEventsCount' => $upcomingEventsCount,
        'upcomingEvents'      => $upcomingEvents,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Event routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    // 👇 ADD THIS ROUTE
    Route::post('/events/{event}/register', [EventController::class, 'register'])
        ->name('events.register');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Show single event (public)
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

require __DIR__.'/auth.php';
