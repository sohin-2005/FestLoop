<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List All Events (Public)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $events = Event::query()
            ->visible()
            ->with('club')
            ->withCount(['activeRegistrations'])
            ->filter($request->only(['search', 'category', 'club', 'time']))
            ->paginate(9)
            ->withQueryString();

        $clubs = Club::approved()->orderBy('name')->get(['id', 'name', 'slug']);
        $spotlight = Event::visible()->upcoming()->with('club')->orderBy('start_time')->first();

        return view('events.index', compact('events', 'clubs', 'spotlight'));
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Search Events
    |--------------------------------------------------------------------------
    */
    public function searchEvents(Request $request)
    {
        $events = Event::query()
            ->visible()
            ->with('club')
            ->withCount('activeRegistrations')
            ->filter($request->only(['search', 'category', 'club', 'time']))
            ->limit(24)
            ->get();

        return response()->json([
            'success' => true,
            'total'   => $events->count(),
            'events'  => $events->map(fn (Event $event) => [
                'id'                  => $event->id,
                'name'                => $event->name,
                'excerpt'             => $event->excerpt,
                'location'            => $event->location,
                'category'            => $event->category,
                'category_label'      => $event->category_label,
                'mode_label'          => $event->mode_label,
                'club_name'           => $event->club?->name,
                'club_slug'           => $event->club?->slug,
                'club_initials'       => $event->club?->initials,
                'start_time'          => $event->start_time->format('M d, Y \a\t g:i A'),
                'start_time_short'    => $event->start_time->format('M d'),
                'start_month'         => $event->start_time->format('M'),
                'start_day'           => $event->start_time->format('d'),
                'banner_url'          => $event->banner_url,
                'registrations_count' => $event->active_registrations_count,
                'spots_left'          => $event->spotsLeft(),
                'is_ongoing'          => $event->isOngoing(),
                'is_past'             => $event->isPast(),
                'url'                 => route('events.show', $event),
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Event Details (Public)
    |--------------------------------------------------------------------------
    */
    public function show(Event $event)
    {
        abort_unless($event->club && $event->club->isApproved(), 404);

        $event->load('club');
        $registration = $event->registrationFor(Auth::user());

        $relatedEvents = Event::visible()
            ->where('id', '!=', $event->id)
            ->where('club_id', $event->club_id)
            ->upcoming()
            ->orderBy('start_time')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'registration', 'relatedEvents'));
    }

    /*
    |--------------------------------------------------------------------------
    | Student Registers for Event
    |--------------------------------------------------------------------------
    */
    public function register(Event $event)
    {
        $user = Auth::user();

        if (! $event->registrationOpen()) {
            return back()->with('error', 'Registration for this event is closed.');
        }

        if ($event->usesExternalRegistration()) {
            return redirect($event->external_registration_url);
        }

        $registration = $event->registerUser($user);

        $message = match ($registration->status) {
            Registration::WAITLISTED => "You're on the waitlist — we'll notify you if a spot opens up.",
            Registration::PENDING    => 'Registration submitted — the club will review and confirm your spot.',
            default                  => "You're registered! See you there.",
        };

        return back()->with('success', $message);
    }

    public function cancelRegistration(Event $event)
    {
        $event->cancelRegistration(Auth::user());

        return back()->with('success', 'Your registration was cancelled.');
    }
}
