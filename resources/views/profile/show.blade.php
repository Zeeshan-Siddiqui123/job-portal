@extends('layouts.app')

@section('title', $user->name . ' - Profile | ' . $portalSettings->site_name)

@section('content')

<div class="profile-card">
    <div class="profile-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap; margin-bottom: 28px;">
        <div style="display: flex; gap: 18px; align-items: center;">
            <div class="avatar-circle">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">{{ $user->name }}</h1>
                <p style="color: var(--primary); font-size: 1rem; font-weight: 600; margin: 0 0 6px 0;">{{ $user->headline ?? 'Career Professional' }}</p>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span><i class="fas fa-map-marker-alt me-1" style="color: var(--primary);"></i> {{ $user->location ?? 'Punjab, Pakistan' }}</span>
                    <span style="color: var(--border-color);">&bull;</span>
                    <span class="role-badge role-{{ $user->role }}">{{ str_replace('_', ' ', $user->role) }}</span>
                </p>
            </div>
        </div>

        @if(Auth::id() === $user->id)
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline"><i class="fas fa-edit me-1"></i> Edit Profile</a>
        </div>
        @endif
    </div>

    @if($user->bio)
    <div style="margin-bottom: 28px;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">About / Summary</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; white-space: pre-line;">{{ $user->bio }}</p>
    </div>
    @endif

    @if($user->skills)
    <div style="margin-bottom: 28px;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">Technical Skills & Expertise</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach(explode(',', $user->skills) as $skill)
            <span style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-full); padding: 5px 12px; font-size: 0.82rem; font-weight: 600; color: var(--text-main);">
                {{ trim($skill) }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="form-grid" style="background: var(--bg-subtle); padding: 20px 24px; border: 1px solid var(--border-color); border-radius: var(--radius);">
        <div>
            <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 4px;">Contact Email</div>
            <strong style="font-size: 0.92rem; color: var(--text-main);">{{ $user->email }}</strong>
        </div>

        <div>
            <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 4px;">Phone Number</div>
            <strong style="font-size: 0.92rem; color: var(--text-main);">{{ $user->phone ?? 'Not provided' }}</strong>
        </div>

        @if($user->resume_link)
        <div>
            <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 4px;">Portfolio / Resume</div>
            <a href="{{ $user->resume_link }}" target="_blank" rel="noopener noreferrer" style="font-weight: 600; font-size: 0.9rem; word-break: break-all;">
                <i class="fas fa-external-link-alt me-1"></i> {{ $user->resume_link }}
            </a>
        </div>
        @endif

        @if($user->company_name)
        <div>
            <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 4px;">Company / Organization</div>
            <strong style="font-size: 0.92rem; color: var(--text-main);">{{ $user->company_name }}</strong>
        </div>
        @endif
    </div>
</div>

@endsection
