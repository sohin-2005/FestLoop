<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $clubs = Club::approved()
            ->withCount(['events', 'followers'])
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$term}%")->orWhere('tagline', 'like', "%{$term}%"));
            })
            ->orderBy('name')
            ->get();

        return view('clubs.index', [
            'clubs'      => $clubs,
            'categories' => Club::CATEGORIES,
        ]);
    }

    public function show(Club $club)
    {
        abort_unless($club->isApproved(), 404);

        $club->load(['achievements', 'roadmapItems', 'announcements']);

        $upcomingEvents = $club->events()->visible()->upcoming()->orderBy('start_time')->get();
        $pastEvents = $club->events()->visible()->past()->orderByDesc('start_time')->take(6)->get();
        $followerCount = $club->followers()->count();
        $isFollowing = $club->isFollowedBy(Auth::user());

        return view('clubs.show', compact('club', 'upcomingEvents', 'pastEvents', 'followerCount', 'isFollowing'));
    }

    public function follow(Club $club)
    {
        $club->followers()->syncWithoutDetaching([Auth::id()]);

        return back()->with('success', "You're now following {$club->name}. New events will show up on your dashboard.");
    }

    public function unfollow(Club $club)
    {
        $club->followers()->detach(Auth::id());

        return back()->with('success', "Unfollowed {$club->name}.");
    }
}
