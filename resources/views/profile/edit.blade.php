@extends('layouts.app')

@section('title', 'Edit Profile | ' . $portalSettings->site_name)

@section('content')
<div class="form-shell">
    <div class="form-panel">
        <div style="margin-bottom: 24px;">
            <p class="eyebrow" style="margin-bottom: 4px;">Account Settings</p>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;">Edit Profile Details</h1>
            <p>Keep your professional information, contact details, and resume link up to date.</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="headline">Professional Headline</label>
                <input type="text" id="headline" name="headline" class="form-control" value="{{ old('headline', $user->headline) }}" placeholder="e.g. Senior Laravel Developer / Hiring Manager">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+92 300 1234567">
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $user->location) }}" placeholder="e.g. Lahore, Pakistan">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="skills">Skills & Expertise (comma separated)</label>
                <input type="text" id="skills" name="skills" class="form-control" value="{{ old('skills', $user->skills) }}" placeholder="Laravel, PHP, MySQL, JavaScript, HTML5">
                <p class="field-help">Separate individual skills with commas.</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="resume_link">Resume / Portfolio Link</label>
                <input type="url" id="resume_link" name="resume_link" class="form-control" value="{{ old('resume_link', $user->resume_link) }}" placeholder="https://github.com/username or LinkedIn link">
                <p class="field-help">Direct link to your portfolio, GitHub, or public resume document.</p>
            </div>

            @if($user->isEmployer())
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name', $user->company_name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_website">Company Website</label>
                    <input type="url" id="company_website" name="company_website" class="form-control" value="{{ old('company_website', $user->company_website) }}" placeholder="https://example.com">
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label" for="bio">Biography / Summary</label>
                <textarea id="bio" name="bio" rows="4" class="form-control" placeholder="Share a brief overview of your background and achievements...">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('profile.show') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save me-1"></i> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
