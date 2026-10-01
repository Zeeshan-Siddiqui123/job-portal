@extends('layouts.app')

@section('title', 'Candidate Dashboard | ' . $portalSettings->site_name)

@section('content')

<div style="margin-bottom: 28px;">
    <p class="eyebrow" style="margin-bottom: 4px;"><i class="fas fa-user-tie me-1"></i> Job Seeker Portal</p>
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">Welcome back, {{ Auth::user()->name }}</h1>
    <p>Track your applications, monitor status updates, and discover new openings.</p>
</div>

<!-- Stats Counters -->
<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Total Applied</div>
        <div class="stat-num" style="color: var(--primary);">{{ $stats['total_applied'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Pending Review</div>
        <div class="stat-num">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Shortlisted</div>
        <div class="stat-num" style="color: var(--success);">{{ $stats['shortlisted'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Hired</div>
        <div class="stat-num" style="color: var(--primary);">{{ $stats['hired'] }}</div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Applications History -->
    <div>
        <div class="dash-card">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
                <i class="fas fa-file-alt me-2" style="color: var(--primary);"></i> My Application History
            </h2>

            @if($myApplications->isEmpty())
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                <div style="width: 48px; height: 48px; border-radius: var(--radius-full); background: var(--bg-subtle); display: grid; place-items: center; margin: 0 auto 12px; color: var(--text-sub);">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 style="font-size: 1.05rem; margin-bottom: 4px;">No applications submitted yet</h3>
                <p style="font-size: 0.9rem;">Start exploring open positions that match your background.</p>
                <a href="{{ route('jobs.index') }}" class="btn btn-primary" style="margin-top: 1rem;">Browse Jobs Now</a>
            </div>
            @else

            <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myApplications as $app)
                        <tr>
                            <td><strong><a href="{{ route('jobs.show', $app->job->id) }}">{{ $app->job->title }}</a></strong></td>
                            <td style="color: var(--text-muted);">{{ $app->job->company }}</td>
                            <td style="font-size: 0.82rem; color: var(--text-sub);">{{ $app->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge-status status-{{ $app->status }}">{{ $app->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('jobs.show', $app->job->id) }}" class="btn btn-outline btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @endif
        </div>
    </div>

    <!-- Recommended Jobs & Notifications -->
    <div>
        <!-- In-app notifications box -->
        <x-notifications :notifications="$notifications" />

        <!-- Recommended Jobs -->
        <div class="dash-card">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
                <i class="fas fa-lightbulb me-2" style="color: var(--primary);"></i> Recommended Jobs
            </h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($recommendedJobs as $recJob)
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <div style="font-weight: 600; font-size: 0.92rem; color: var(--text-main); margin-bottom: 2px;">
                        <a href="{{ route('jobs.show', $recJob->id) }}">{{ $recJob->title }}</a>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">{{ $recJob->company }} &bull; {{ $recJob->location }}</div>
                </div>
                @empty
                <p style="font-size: 0.85rem; color: var(--text-sub); margin: 0;">No recommendations at this time.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
