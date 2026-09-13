<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubApprovalController extends Controller
{
    public function index()
    {
        $pending = Club::where('status', 'pending')->withCount('coordinators')->latest()->get();
        $approved = Club::approved()->withCount(['events', 'followers'])->orderBy('name')->get();

        return view('admin.clubs.index', compact('pending', 'approved'));
    }

    public function approve(Club $club)
    {
        $club->update(['status' => 'approved', 'approved_at' => now()]);

        return back()->with('success', "{$club->name} approved — it's now live.");
    }

    public function reject(Club $club)
    {
        $club->update(['status' => 'rejected']);

        return back()->with('success', "{$club->name} rejected.");
    }
}
