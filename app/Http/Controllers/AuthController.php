<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (!PortalSetting::current()->registration_open) {
            return redirect()->route('login')->with('error', 'New account registration is currently paused.');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        abort_unless(PortalSetting::current()->registration_open, 403, 'New account registration is currently paused.');
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:job_seeker,employer',
            'headline' => 'nullable|string|max:150',
            'company_name' => 'nullable|string|max:150',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'headline' => $validated['headline'] ?? ($validated['role'] === 'employer' ? 'Hiring Manager at ' . ($validated['company_name'] ?? 'Company') : 'Software Engineer / Job Seeker'),
            'company_name' => $validated['company_name'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to JobPortal.');
    }

    /**
     * Demo Quick Login for testing different user roles instantly.
     */
    public function demoLogin($role)
    {
        $user = User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Logged in as Demo ' . ucfirst($role) . ' (' . $user->name . ')');
        }

        return back()->with('error', 'Demo user for role not found.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('jobs.index')->with('success', 'Logged out successfully.');
    }
}
