<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // List all events
   // List all events with filters + pagination
public function index(Request $request)
{
    $query = Event::query()
        ->withCount('registrations')      // gives $event->registrations_count
        ->orderBy('start_time', 'asc');

    // Search filter
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

    // Now we get a paginator instead of a simple collection
    $events = $query->paginate(9)->withQueryString(); // 9 per page

    return view('events.index', compact('events'));
}


    // Show create form
    public function create()
    {
        // Later: check if Auth::user()->role === 'coordinator'
        return view('events.create');
    }

    // Save new event
    // Save new event
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'required|string',
            'location'             => 'required|string|max:255',
            'start_time'           => 'required|date',
            'end_time'             => 'nullable|date|after_or_equal:start_time',
    
            'banner_image'         => 'nullable|image|max:5120', // 5MB
            'category'             => 'nullable|string|max:100',
            'venue_details'        => 'nullable|string',
            'max_participants'     => 'nullable|integer|min:1',
            'registration_deadline'=> 'nullable|date|after_or_equal:now',
            'requires_approval'    => 'nullable|boolean',
            'organizer'            => 'nullable|string|max:255',
            'contact_email'        => 'nullable|email|max:255',
            'contact_phone'        => 'nullable|string|max:50',
            'rules'                => 'nullable|string',
        ]);
    
        // checkbox: if not checked, it won't come in request
        $data['requires_approval'] = $request->has('requires_approval');
    
        // handle banner image upload
        if ($request->hasFile('banner_image')) {
            // store in storage/app/public/event-banners
            $path = $request->file('banner_image')->store('event-banners', 'public');
            $data['banner_image'] = $path;
        }
    
        Event::create($data);
    
        return redirect()
            ->route('events.index')
            ->with('success', 'Event created successfully!');
    }
    
    // Show single event + registration button
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

    // Register current user
    public function register(Event $event)
    {
        $user = Auth::user();

        // basic checks
        if ($event->registration_deadline && now()->gt($event->registration_deadline)) {
            return back()->with('error', 'Registration closed.');
        }

        if ($event->capacity && $event->registrations()->count() >= $event->capacity) {
            return back()->with('error', 'Event is full.');
        }

        Registration::firstOrCreate([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        return back()->with('success', 'You are registered for this event!');
    }

    // Edit / update / delete – for coordinators
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
{
    $data = $request->validate([
        'name'                 => 'required|string|max:255',
        'description'          => 'required|string',
        'location'             => 'required|string|max:255',
        'start_time'           => 'required|date',
        'end_time'             => 'nullable|date|after_or_equal:start_time',

        'banner_image'         => 'nullable|image|max:5120',
        'category'             => 'nullable|string|max:100',
        'venue_details'        => 'nullable|string',
        'max_participants'     => 'nullable|integer|min:1',
        'registration_deadline'=> 'nullable|date|after_or_equal:now',
        'requires_approval'    => 'nullable|boolean',
        'organizer'            => 'nullable|string|max:255',
        'contact_email'        => 'nullable|email|max:255',
        'contact_phone'        => 'nullable|string|max:50',
        'rules'                => 'nullable|string',
    ]);

    $data['requires_approval'] = $request->has('requires_approval');

    if ($request->hasFile('banner_image')) {
        $path = $request->file('banner_image')->store('event-banners', 'public');
        $data['banner_image'] = $path;
        // later you can delete old image if you want
    }

    $event->update($data);

    return redirect()
        ->route('events.show', $event)
        ->with('success', 'Event updated successfully!');
}


    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }
}
