<?php

namespace App\Http\Controllers;

use App\Models\Coordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoordinatorAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('coordinator.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:coordinators,email',
            'password' => 'required|min:6|confirmed',
        ]);

        Coordinator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/coordinator/login')->with('status', 'Account created. Please login.');
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

        if (Auth::guard('coordinator')->attempt($credentials)) {
            return redirect()->intended('/coordinator/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::guard('coordinator')->logout();
        return redirect('/coordinator/login')->with('status', 'Logged out successfully.');
    }
    
}
