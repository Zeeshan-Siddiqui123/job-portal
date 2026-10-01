@extends('layouts.app')

@section('title', 'Edit Profile | JobPortal')

@section('content')
<div class="form-shell ">
    <div class="form-panel">
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Edit Profile Details</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Keep your profile information, skills, and resume link up to date.</p>

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

            <div class="form-grid" style="gap: 1rem;">
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
                <label class="form-label" for="skills">Skills & Tools (comma separated)</label>
                <input type="text" id="skills" name="skills" class="form-control" value="{{ old('skills', $user->skills) }}" placeholder="Laravel, PHP, MySQL, JavaScript, HTML5">
            </div>

            <div class="form-group">
                <label class="form-label" for="resume_link">Resume / Portfolio Link</label>
                <input type="url" id="resume_link" name="resume_link" class="form-control" value="{{ old('resume_link', $user->resume_link) }}" placeholder="https://github.com/username or LinkedIn link">
            </div>

            @if($user->isEmployer())
            <div class="form-grid" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name', $user->company_name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_website">Company Website</label>
                    <input type="url" id="company_website" name="company_website" class="form-control" value="{{ old('company_website', $user->company_website) }}">
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label" for="bio">Biography / About Me</label>
                <textarea id="bio" name="bio" rows="4" class="form-control">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 1rem;">
                <i class="fas fa-save me-1"></i> Save Profile
            </button>
        </form>
    </div>
</div>
@endsection
