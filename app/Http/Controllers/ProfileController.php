<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show logged in user profile or candidate profile.
     */
    public function show($id = null)
    {
        $user = $id ? User::findOrFail($id) : Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $viewer = Auth::user();
        $canView = $viewer->id === $user->id || $viewer->isAdmin();
        if (! $canView && $viewer->isEmployer() && $user->isJobSeeker()) {
            $canView = $user->applications()
                ->whereHas('job', fn ($query) => $query->where('employer_id', $viewer->id))
                ->exists();
        }
        abort_unless($canView, 403);

        return view('profile.show', compact('user'));
    }

    /**
     * Edit user profile form.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile details in DB.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'headline' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:150',
            'company_website' => 'nullable|url',
            'resume_link' => 'nullable|url',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
