<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CoordinatorAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

/*
|--------------------------------------------------------------------------
| Coordinator Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest:coordinator')->group(function () {

    Route::get('/coordinator/login', [CoordinatorAuthController::class, 'showLoginForm'])
        ->name('coordinator.login');

    Route::post('/coordinator/login', [CoordinatorAuthController::class, 'login']);

    Route::get('/coordinator/register', [CoordinatorAuthController::class, 'showRegisterForm'])
        ->name('coordinator.register');

    Route::post('/coordinator/register', [CoordinatorAuthController::class, 'register']);
});


/*
|--------------------------------------------------------------------------
| Coordinator Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:coordinator')->prefix('coordinator')->name('coordinator.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $events = Event::where('coordinator_id', auth('coordinator')->id())->get();
        return view('coordinator.dashboard', compact('events'));
    })->name('dashboard');

    // Logout
    Route::post('/logout', [CoordinatorAuthController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Coordinator Event Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('events')->name('events.')->group(function () {

        Route::get('/', [EventController::class, 'coordinatorIndex'])
            ->name('index');

        Route::get('/create', [EventController::class, 'coordinatorCreate'])
            ->name('create');

        Route::post('/', [EventController::class, 'coordinatorStore'])
            ->name('store');

        Route::get('/{event}/edit', [EventController::class, 'coordinatorEdit'])
            ->name('edit');

        Route::patch('/{event}', [EventController::class, 'coordinatorUpdate'])
            ->name('update');

        Route::delete('/{event}', [EventController::class, 'coordinatorDestroy'])
            ->name('destroy');
    });
});


/*
|--------------------------------------------------------------------------
| Public Routes (for both Guests & Students)
|--------------------------------------------------------------------------
*/
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'searchEvents'])->name('events.search');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');


/*
|--------------------------------------------------------------------------
| Student Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {

        // If coordinator is logged in → redirect them away
        if (Auth::guard('coordinator')->check()) {
            return redirect()->route('coordinator.dashboard');
        }

        $totalEvents = Event::count();
        $upcomingEventsCount = Event::where('start_time', '>=', now())->count();
        $upcomingEvents = Event::where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalEvents',
            'upcomingEventsCount',
            'upcomingEvents'
        ));
    })->name('dashboard');

    // Student event registration
    Route::post('/events/{event}/register', [EventController::class, 'register'])
        ->name('events.register');

    // Student profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


require __DIR__ . '/auth.php';
