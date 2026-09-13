<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Event;
use App\Models\Registration;
use App\Notifications\NewClubEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    private function club(): Club
    {
        $club = Auth::guard('coordinator')->user()->club;
        abort_unless($club, 403, 'No club is linked to this coordinator account yet.');

        return $club;
    }

    public function index()
    {
        $events = $this->club()->events()
            ->withCount('activeRegistrations')
            ->orderByDesc('start_time')
            ->get();

        return view('coordinator.events.index', compact('events'));
    }

    public function create()
    {
        return view('coordinator.events.create');
    }

    private function rules(): array
    {
        return [
            'name'                       => 'required|string|max:255',
            'description'                => 'required|string',
            'category'                   => ['required', Rule::in(array_keys(Event::CATEGORIES))],
            'mode'                       => ['required', Rule::in(array_keys(Event::MODES))],
            'location'                   => 'required|string|max:255',
            'start_time'                 => 'required|date',
            'end_time'                   => 'nullable|date|after_or_equal:start_time',
            'banner_image'               => 'nullable|image|max:5120',
            'venue_details'              => 'nullable|string',
            'max_participants'           => 'nullable|integer|min:1',
            'registration_deadline'      => 'nullable|date',
            'requires_approval'          => 'nullable|boolean',
            'external_registration_url'  => 'nullable|url|max:255',
            'contact_email'              => 'nullable|email|max:255',
            'contact_phone'              => 'nullable|string|max:50',
            'rules'                      => 'nullable|string',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['requires_approval'] = $request->boolean('requires_approval');
        $data['coordinator_id'] = Auth::guard('coordinator')->id();
        $data['club_id'] = $this->club()->id;

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('event-banners', 'public');
        }

        $event = Event::create($data);

        foreach ($this->club()->followers()->get() as $follower) {
            $follower->notify(new NewClubEvent($event));
        }

        return redirect()->route('coordinator.events.index')->with('success', 'Event published!');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_unless($event->club_id === $this->club()->id, 403);
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        return view('coordinator.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $data = $request->validate($this->rules());
        $data['requires_approval'] = $request->boolean('requires_approval');

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('event-banners', 'public');
        }

        $event->update($data);

        return redirect()->route('coordinator.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $event->delete();

        return redirect()->route('coordinator.events.index')->with('success', 'Event deleted.');
    }

    public function recap(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $data = $request->validate(['recap' => 'required|string']);
        $event->update($data);

        return back()->with('success', 'Recap saved — it now shows on your club page.');
    }

    /*
    |--------------------------------------------------------------------------
    | Registration management
    |--------------------------------------------------------------------------
    */
    public function registrations(Event $event)
    {
        $this->authorizeEvent($event);

        $registrations = $event->registrations()->with('user')->orderBy('status')->orderBy('created_at')->get();

        return view('coordinator.events.registrations', compact('event', 'registrations'));
    }

    public function updateRegistration(Request $request, Event $event, Registration $registration)
    {
        $this->authorizeEvent($event);
        abort_unless($registration->event_id === $event->id, 404);

        $data = $request->validate(['status' => ['required', Rule::in([
            Registration::REGISTERED, Registration::PENDING, Registration::WAITLISTED, Registration::REJECTED,
        ])]]);

        $registration->update($data);

        return back()->with('success', 'Registration updated.');
    }
}
