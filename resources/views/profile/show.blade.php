@extends('layouts.app')

@section('title', $user->name . ' - Profile | JobPortal')

@section('styles')
<style>
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .avatar-circle {
        width: 80px;
        height: 80px;
        background: var(--bg-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-main);
        font-size: 2.2rem;
        font-weight: 700;
    }
</style>
@endsection

@section('content')

<div class="profile-card">
    <div class="profile-header">
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <div class="avatar-circle">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $user->name }}</h1>
                <p style="color: var(--primary-light); font-size: 1.05rem; font-weight: 600;">{{ $user->headline ?? 'Software Professional' }}</p>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                    <i class="fas fa-map-marker-alt me-1" style="color: var(--accent);"></i> {{ $user->location ?? 'Punjab, Pakistan' }} • 
                    <span class="role-badge role-{{ $user->role }} ms-1">{{ str_replace('_', ' ', $user->role) }}</span>
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
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">About / Biography</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; white-space: pre-line;">{{ $user->bio }}</p>
    </div>
    @endif

    @if($user->skills)
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">Technical Skills & Capabilities</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.6rem;">
            @foreach(explode(',', $user->skills) as $skill)
            <span style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.35rem 0.85rem; font-size: 0.88rem; color: var(--primary-light);">
                <i class="fas fa-check-circle me-1" style="color: var(--accent);"></i> {{ trim($skill) }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="form-grid" style="gap: 1.5rem; background: var(--bg-subtle); padding: 1.5rem; border: 1px solid var(--border-color);">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-sub);">CONTACT EMAIL</div>
            <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $user->email }}</strong>
        </div>

        <div>
            <div style="font-size: 0.8rem; color: var(--text-sub);">PHONE NUMBER</div>
            <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $user->phone ?? 'Not provided' }}</strong>
        </div>

        @if($user->resume_link)
        <div>
            <div style="font-size: 0.8rem; color: var(--text-sub);">RESUME / PORTFOLIO LINK</div>
            <a href="{{ $user->resume_link }}" target="_blank" style="color: var(--primary-light); font-weight: 600;"><i class="fas fa-external-link-alt me-1"></i> {{ $user->resume_link }}</a>
        </div>
        @endif

        @if($user->company_name)
        <div>
            <div style="font-size: 0.8rem; color: var(--text-sub);">COMPANY ORGANIZATION</div>
            <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $user->company_name }}</strong>
        </div>
        @endif
    </div>
</div>

@endsection
