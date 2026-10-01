@extends('layouts.app')

@section('title', 'Candidate Dashboard | JobPortal')

@section('styles')
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
    }

    .stat-num {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .dash-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.75rem;
        margin-bottom: 2rem;
    }

    .app-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .app-table th, .app-table td {
        padding: 0.9rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .app-table th {
        color: var(--text-sub);
        font-size: 0.82rem;
        text-transform: uppercase;
        background: var(--bg-subtle);
    }

    .badge-status {
        padding: 0.25rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-Pending { background: var(--bg-subtle); color: var(--text-muted); border: 1px solid var(--border-color); }
    .status-Shortlisted { background: var(--bg-subtle); color: var(--primary); border: 1px solid var(--border-color); }
    .status-Hired { background: var(--bg-subtle); color: var(--text-muted); border: 1px solid var(--border-color); }
    .status-Rejected { background: var(--bg-subtle); color: var(--danger); border: 1px solid var(--border-color); }

    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: var(--primary-light); font-weight: 600; text-transform: uppercase;"><i class="fas fa-user-tie me-1"></i> Job Seeker Portal</div>
    <h1 style="font-size: 2.2rem; font-weight: 800;">Welcome, {{ Auth::user()->name }}</h1>
    <p style="color: var(--text-muted);">Track your job applications, application status updates, and recommended openings.</p>
</div>

<!-- Stats Counters -->
<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">TOTAL APPLIED</div>
        <div class="stat-num" style="color: var(--primary-light);">{{ $stats['total_applied'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">PENDING REVIEW</div>
        <div class="stat-num" style="color: var(--text-muted);">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">SHORTLISTED</div>
        <div class="stat-num" style="color: var(--primary);">{{ $stats['shortlisted'] }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">HIRED</div>
        <div class="stat-num" style="color: var(--text-muted);">{{ $stats['hired'] }}</div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Applications History -->
    <div>
        <div class="dash-card">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-file-alt me-2" style="color: var(--primary-light);"></i> My Application History</h3>

            @if($myApplications->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                <p>You haven't submitted any job applications yet.</p>
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
                        <td style="font-size: 0.85rem; color: var(--text-sub);">{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge-status status-{{ $app->status }}">{{ $app->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('jobs.show', $app->job->id) }}" class="btn btn-outline" style="padding: 0.25rem 0.6rem; font-size: 0.78rem;">
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
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-sparkles me-2" style="color: var(--text-muted);"></i> Recommended Jobs</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($recommendedJobs as $recJob)
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                    <div style="font-weight: 600; color: var(--text-main);"><a href="{{ route('jobs.show', $recJob->id) }}">{{ $recJob->title }}</a></div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">{{ $recJob->company }} • {{ $recJob->location }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
