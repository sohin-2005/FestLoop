<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Registration;

class HomeController extends Controller
{
    /**
     * The front door: an events-first landing page. Everything on it is real
     * data, so the motion never advertises something that isn't there.
     */
    public function __invoke()
    {
        $liveNow = Event::visible()->ongoing()->with('club')->orderBy('start_time')->take(3)->get();

        $upcoming = Event::visible()
            ->upcoming()
            ->with('club')
            ->withCount('activeRegistrations')
            ->orderBy('start_time')
            ->take(4)
            ->get();

        // The logo wall is the hero's motion — real club marks, not stock art.
        $wallClubs = Club::approved()
            ->whereNotNull('logo_path')
            ->inRandomOrder()
            ->get(['name', 'slug', 'short_name', 'logo_path', 'accent_color']);

        $stats = [
            'clubs'         => Club::approved()->count(),
            'events'        => Event::visible()->count(),
            'upcoming'      => Event::visible()->upcoming()->count(),
            'registrations' => Registration::count(),
        ];

        $spotlight = $upcoming->first();

        return view('home', compact('liveNow', 'upcoming', 'wallClubs', 'stats', 'spotlight'));
    }
}
