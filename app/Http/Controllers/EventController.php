<?php

namespace App\Http\Controllers;

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
        $query = Event::query()
            ->withCount('registrations')
            ->orderBy('start_time', 'asc');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Time filter
        if ($time = $request->input('time')) {
            $now = now();

            if ($time === 'upcoming') {
                $query->where('start_time', '>', $now);

            } elseif ($time === 'ongoing') {
                $query->where('start_time', '<=', $now)
                      ->where(function ($q) use ($now) {
                          $q->whereNull('end_time')
                            ->orWhere('end_time', '>=', $now);
                      });

            } elseif ($time === 'past') {
                $query->where(function ($q) use ($now) {
                    $q->whereNotNull('end_time')->where('end_time', '<', $now)
                      ->orWhere(function ($q2) use ($now) {
                          $q2->whereNull('end_time')->where('start_time', '<', $now);
                      });
                });
            }
        }

        $events = $query->paginate(9)->withQueryString();

        return view('events.index', compact('events'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show Create Event Form (Coordinator Only)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('events.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Event (Coordinator Only)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'required|string',
            'location'              => 'required|string|max:255',
            'start_time'            => 'required|date',
            'end_time'              => 'nullable|date|after_or_equal:start_time',

            'banner_image'          => 'nullable|image|max:5120',
            'category'              => 'nullable|string|max:100',
            'venue_details'         => 'nullable|string',
            'max_participants'      => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date',
            'requires_approval'     => 'nullable|boolean',
            'contact_email'         => 'nullable|email',
            'contact_phone'         => 'nullable|string',
            'rules'                 => 'nullable|string',
        ]);

        // Assign event to the logged-in coordinator
        $data['coordinator_id'] = auth('coordinator')->id();
        $data['requires_approval'] = $request->has('requires_approval');

        // Handle banner upload
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('event-banners', 'public');
        }

        Event::create($data);

        return redirect()
            ->route('coordinator.dashboard')
            ->with('success', 'Event created successfully!');
    }


    /*
    |--------------------------------------------------------------------------
    | Show Event Details (Public)
    |--------------------------------------------------------------------------
    */
    public function show(Event $event)
    {
        $alreadyRegistered = false;

        if (Auth::check()) {
            $alreadyRegistered = $event->registrations()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('events.show', compact('event', 'alreadyRegistered'));
    }


    /*
    |--------------------------------------------------------------------------
    | Student Registers for Event
    |--------------------------------------------------------------------------
    */
    public function register(Event $event)
    {
        $user = Auth::user();

        if ($event->registration_deadline && now()->gt($event->registration_deadline)) {
            return back()->with('error', 'Registration closed.');
        }

        if ($event->max_participants && $event->registrations()->count() >= $event->max_participants) {
            return back()->with('error', 'Event is full.');
        }

        Registration::firstOrCreate([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        return back()->with('success', 'You are registered for this event!');
    }


    /*
    |--------------------------------------------------------------------------
    | Coordinator Edit Event
    |--------------------------------------------------------------------------
    */
    public function edit(Event $event)
    {
        if ($event->coordinator_id !== auth('coordinator')->id()) {
            abort(403);
        }

        return view('events.edit', compact('event'));
    }

    /*
    |--------------------------------------------------------------------------
    | Coordinator Update Event
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Event $event)
    {
        if ($event->coordinator_id !== auth('coordinator')->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'required|string',
            'location'              => 'required|string|max:255',
            'start_time'            => 'required|date',
            'end_time'              => 'nullable|date|after_or_equal:start_time',

            'banner_image'          => 'nullable|image|max:5120',
            'category'              => 'nullable|string|max:100',
            'venue_details'         => 'nullable|string',
            'max_participants'      => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date|after_or_equal:now',
            'requires_approval'     => 'nullable|boolean',
            'contact_email'         => 'nullable|email|max:255',
            'contact_phone'         => 'nullable|string|max:50',
            'rules'                 => 'nullable|string',
        ]);

        $data['requires_approval'] = $request->has('requires_approval');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('event-banners', 'public');
        }

        $event->update($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }


    /*
    |--------------------------------------------------------------------------
    | Coordinator Deletes Event
    |--------------------------------------------------------------------------
    */
    public function destroy(Event $event)
    {
        if ($event->coordinator_id !== auth('coordinator')->id()) {
            abort(403);
        }

        $event->delete();

        return redirect()
            ->route('coordinator.dashboard')
            ->with('success', 'Event deleted.');
    }
}
