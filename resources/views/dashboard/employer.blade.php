@extends('layouts.app')

@section('title', 'Employer Dashboard | JobPortal')

@section('styles')
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
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
</style>
@endsection

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <div style="font-size: 0.85rem; color: var(--accent); font-weight: 600; text-transform: uppercase;"><i class="fas fa-building me-1"></i> Employer Portal</div>
        <h1 style="font-size: 2.2rem; font-weight: 800;">{{ Auth::user()->company_name ?? Auth::user()->name }} Dashboard</h1>
        <p style="color: var(--text-muted);">Manage your active job postings and candidate recruitment pipeline.</p>
    </div>
    <div>
        <a href="{{ route('jobs.create') }}" class="btn btn-accent"><i class="fas fa-plus-circle me-1"></i> Post New Job</a>
    </div>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">POSTED JOBS</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent);">{{ $totalPosted }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">TOTAL APPLICATIONS</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary-light);">{{ $totalApps }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">SHORTLISTED CANDIDATES</div>
        <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">{{ $shortlistedApps }}</div>
    </div>
</div>

<!-- Posted Jobs -->
<x-notifications :notifications="$notifications" />

<!-- Posted Jobs -->
<div class="dash-card">
    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-briefcase me-2" style="color: var(--accent);"></i> Active Job Listings</h3>

    @if($postedJobs->isEmpty())
    <p style="color: var(--text-muted); padding: 1rem 0;">No active job listings. Click "Post New Job" above to create one.</p>
    @else
    <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
<table class="app-table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Location</th>
                <th>Type</th>
                <th>Applicants</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($postedJobs as $job)
            <tr>
                <td><strong><a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a></strong></td>
                <td style="color: var(--text-muted);">{{ $job->location }}</td>
                <td><span style="font-size: 0.8rem; background: var(--bg-subtle); padding: 0.2rem 0.5rem;">{{ $job->type }}</span></td>
                <td><strong style="color: var(--primary-light);">{{ $job->applications_count }}</strong></td>
                <td>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; background: {{ $job->status === 'Open' ? 'var(--bg-subtle)' : 'var(--bg-subtle)' }}; color: {{ $job->status === 'Open' ? 'var(--text-muted)' : 'var(--danger)' }};">
                        {{ $job->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-outline" style="padding: 0.25rem 0.6rem; font-size: 0.78rem;">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    @endif
</div>

<!-- Candidate Applications Pipeline -->
<div class="dash-card">
    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-users me-2" style="color: var(--primary-light);"></i> Candidate Applications Pipeline</h3>

    @if($applications->isEmpty())
    <p style="color: var(--text-muted); padding: 1rem 0;">No applications received yet.</p>
    @else
    <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
<table class="app-table">
        <thead>
            <tr>
                <th>Candidate</th>
                <th>Applied Job</th>
                <th>Resume / Link</th>
                <th>Application Status</th>
                <th>Change Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>
                    <strong style="color: var(--text-main);">{{ $app->jobSeeker->name }}</strong><br>
                    <span style="font-size: 0.8rem; color: var(--text-sub);">{{ $app->jobSeeker->email }}</span>
                    <br><x-application-details :application="$app" />
                </td>
                <td><strong>{{ $app->job->title }}</strong></td>
                <td>
                    @if($app->resume_url)
                    <a href="{{ $app->resume_url }}" target="_blank" style="color: var(--primary-light); font-size: 0.85rem;"><i class="fas fa-external-link-alt"></i> View Resume</a>
                    @else
                    <span style="color: var(--text-sub); font-size: 0.82rem;">Not provided</span>
                    @endif
                </td>
                <td>
                    <span style="font-size: 0.8rem; font-weight: 700; padding: 0.25rem 0.6rem; background: var(--bg-subtle);">
                        {{ $app->status }}
                    </span>
                </td>
                <td>
                    <form action="{{ route('applications.status', $app->id) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                        @csrf
                        <x-custom-dropdown class="custom-dropdown--compact">
                        <select name="status" class="form-control" aria-label="Application status">
                            <option value="Pending" {{ $app->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Shortlisted" {{ $app->status == 'Shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="Hired" {{ $app->status == 'Hired' ? 'selected' : '' }}>Hired</option>
                            <option value="Rejected" {{ $app->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        </x-custom-dropdown>
                        <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">Update</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    @endif
</div>

@endsection
