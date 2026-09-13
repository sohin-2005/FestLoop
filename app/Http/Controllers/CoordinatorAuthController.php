<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Coordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CoordinatorAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('coordinator.register', ['categories' => Club::CATEGORIES]);
    }

    /**
     * A brand new club registers itself along with its first coordinator account.
     * The club starts as "pending" until an admin approves it — this keeps the
     * public directory free of spam / duplicate club listings.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'club_name'  => 'required|string|max:255',
            'category'   => 'required|string|in:'.implode(',', array_keys(Club::CATEGORIES)),
            'tagline'    => 'nullable|string|max:255',
            'name'       => 'required|string|max:255',
            'position'   => 'nullable|string|max:255',
            'email'      => 'required|email|unique:coordinators,email',
            'password'   => 'required|min:6|confirmed',
        ]);

        $coordinator = DB::transaction(function () use ($data) {
            $club = Club::create([
                'name'     => $data['club_name'],
                'category' => $data['category'],
                'tagline'  => $data['tagline'] ?? null,
                'status'   => 'pending',
            ]);

            return Coordinator::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'club_id'  => $club->id,
                'position' => $data['position'] ?? 'Coordinator',
            ]);
        });

        return redirect('/coordinator/login')->with('status',
            "Thanks! \"{$coordinator->club->name}\" is registered and pending admin approval. You can log in now and set up your club page while you wait."
        );
    }

    public function showLoginForm()
    {
        return view('coordinator.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('coordinator')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('coordinator.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('coordinator')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/coordinator/login')->with('status', 'Logged out successfully.');
    }
}
