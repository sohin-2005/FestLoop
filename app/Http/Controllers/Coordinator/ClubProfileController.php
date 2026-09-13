<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubProfileController extends Controller
{
    private function club()
    {
        $club = Auth::guard('coordinator')->user()->club;
        abort_unless($club, 403, 'No club is linked to this coordinator account yet.');

        return $club;
    }

    public function edit()
    {
        return view('coordinator.club.edit', ['club' => $this->club()]);
    }

    public function update(Request $request)
    {
        $club = $this->club();

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'short_name'      => 'nullable|string|max:12',
            'tagline'         => 'nullable|string|max:255',
            'category'        => 'required|string|in:'.implode(',', array_keys(Club::CATEGORIES)),
            'about'           => 'nullable|string',
            'mission'         => 'nullable|string',
            'accent_color'    => 'nullable|string|max:7',
            'founded_year'    => 'nullable|integer|min:1950|max:'.date('Y'),
            'email'           => 'nullable|email|max:255',
            'website'         => 'nullable|url|max:255',
            'instagram'       => 'nullable|string|max:255',
            'linkedin'        => 'nullable|string|max:255',
            'faculty_advisor' => 'nullable|string|max:255',
            'logo_image'      => 'nullable|image|max:2048',
            'cover_image'     => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('logo_image')) {
            $data['logo_path'] = $request->file('logo_image')->store('club-logos', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_path'] = $request->file('cover_image')->store('club-covers', 'public');
        }

        $club->update($data);

        return back()->with('success', 'Club profile updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Achievements
    |--------------------------------------------------------------------------
    */
    public function storeAchievement(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'achieved_on' => 'nullable|date',
        ]);

        $this->club()->achievements()->create($data);

        return back()->with('success', 'Achievement added.');
    }

    public function destroyAchievement($id)
    {
        $this->club()->achievements()->whereKey($id)->delete();

        return back()->with('success', 'Achievement removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Roadmap
    |--------------------------------------------------------------------------
    */
    public function storeRoadmapItem(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'target_label' => 'nullable|string|max:100',
            'status'       => 'required|in:planned,in_progress,done',
        ]);

        $this->club()->roadmapItems()->create($data);

        return back()->with('success', 'Roadmap item added.');
    }

    public function destroyRoadmapItem($id)
    {
        $this->club()->roadmapItems()->whereKey($id)->delete();

        return back()->with('success', 'Roadmap item removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Announcements
    |--------------------------------------------------------------------------
    */
    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        $data['is_pinned'] = $request->boolean('is_pinned');

        $this->club()->announcements()->create($data);

        return back()->with('success', 'Announcement posted.');
    }

    public function destroyAnnouncement($id)
    {
        $this->club()->announcements()->whereKey($id)->delete();

        return back()->with('success', 'Announcement removed.');
    }
}
