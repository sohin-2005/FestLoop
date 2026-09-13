<?php

use App\Http\Controllers\Admin\ClubApprovalController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\Coordinator\ClubProfileController;
use App\Http\Controllers\Coordinator\EventController as CoordinatorEventController;
use App\Http\Controllers\CoordinatorAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Models\Club;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes — the shared events & club directory
|--------------------------------------------------------------------------
*/
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'searchEvents'])->name('events.search');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

/*
|--------------------------------------------------------------------------
| Coordinator (club) auth & protected area
|--------------------------------------------------------------------------
*/
Route::middleware('guest:coordinator')->group(function () {
    Route::get('/coordinator/login', [CoordinatorAuthController::class, 'showLoginForm'])->name('coordinator.login');
    Route::post('/coordinator/login', [CoordinatorAuthController::class, 'login']);
    Route::get('/coordinator/register', [CoordinatorAuthController::class, 'showRegisterForm'])->name('coordinator.register');
    Route::post('/coordinator/register', [CoordinatorAuthController::class, 'register']);
});

Route::middleware('auth:coordinator')->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::get('/dashboard', function () {
        $coordinator = Auth::guard('coordinator')->user();
        $club = $coordinator->club;

        $events = $club
            ? $club->events()->withCount('activeRegistrations')->orderByDesc('start_time')->get()
            : collect();

        $upcoming = $events->filter(fn ($e) => $e->isUpcoming() || $e->isOngoing());
        $stats = [
            'total_events'     => $events->count(),
            'upcoming_events'  => $upcoming->count(),
            'total_registered' => $events->sum('active_registrations_count'),
            'followers'        => $club?->followers()->count() ?? 0,
        ];

        return view('coordinator.dashboard', compact('club', 'events', 'stats'));
    })->name('dashboard');

    Route::post('/logout', [CoordinatorAuthController::class, 'logout'])->name('logout');

    Route::get('/club', [ClubProfileController::class, 'edit'])->name('club.edit');
    Route::patch('/club', [ClubProfileController::class, 'update'])->name('club.update');
    Route::post('/club/achievements', [ClubProfileController::class, 'storeAchievement'])->name('club.achievements.store');
    Route::delete('/club/achievements/{id}', [ClubProfileController::class, 'destroyAchievement'])->name('club.achievements.destroy');
    Route::post('/club/roadmap', [ClubProfileController::class, 'storeRoadmapItem'])->name('club.roadmap.store');
    Route::delete('/club/roadmap/{id}', [ClubProfileController::class, 'destroyRoadmapItem'])->name('club.roadmap.destroy');
    Route::post('/club/announcements', [ClubProfileController::class, 'storeAnnouncement'])->name('club.announcements.store');
    Route::delete('/club/announcements/{id}', [ClubProfileController::class, 'destroyAnnouncement'])->name('club.announcements.destroy');

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [CoordinatorEventController::class, 'index'])->name('index');
        Route::get('/create', [CoordinatorEventController::class, 'create'])->name('create');
        Route::post('/', [CoordinatorEventController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [CoordinatorEventController::class, 'edit'])->name('edit');
        Route::patch('/{event}', [CoordinatorEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [CoordinatorEventController::class, 'destroy'])->name('destroy');
        Route::post('/{event}/recap', [CoordinatorEventController::class, 'recap'])->name('recap');
        Route::get('/{event}/registrations', [CoordinatorEventController::class, 'registrations'])->name('registrations');
        Route::patch('/{event}/registrations/{registration}', [CoordinatorEventController::class, 'updateRegistration'])->name('registrations.update');
    });
});

/*
|--------------------------------------------------------------------------
| Admin — approves newly registered clubs
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/clubs', [ClubApprovalController::class, 'index'])->name('clubs.index');
    Route::post('/clubs/{club}/approve', [ClubApprovalController::class, 'approve'])->name('clubs.approve');
    Route::post('/clubs/{club}/reject', [ClubApprovalController::class, 'reject'])->name('clubs.reject');
});

/*
|--------------------------------------------------------------------------
| Student Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $registeredEvents = $user->registeredEvents()->with('club')->orderBy('start_time')->get();
        $upcomingRegistrations = $registeredEvents->filter(fn ($e) => $e->isUpcoming() || $e->isOngoing());

        $followedClubIds = $user->followedClubs()->pluck('clubs.id');
        $followedClubs = $user->followedClubs()->withCount('events')->get();

        $forYouEvents = Event::visible()
            ->with('club')
            ->upcoming()
            ->when($followedClubIds->isNotEmpty(), fn ($q) => $q->whereIn('club_id', $followedClubIds))
            ->orderBy('start_time')
            ->take(6)
            ->get();

        if ($forYouEvents->isEmpty()) {
            $forYouEvents = Event::visible()->with('club')->upcoming()->orderBy('start_time')->take(6)->get();
        }

        return view('dashboard', compact(
            'registeredEvents', 'upcomingRegistrations', 'followedClubs', 'forYouEvents'
        ));
    })->name('dashboard');

    // Student event registration
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/register', [EventController::class, 'cancelRegistration'])->name('events.register.cancel');

    // Follow / unfollow clubs
    Route::post('/clubs/{club}/follow', [ClubController::class, 'follow'])->name('clubs.follow');
    Route::delete('/clubs/{club}/follow', [ClubController::class, 'unfollow'])->name('clubs.unfollow');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'readAndRedirect'])->name('notifications.read');

    // Student profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
