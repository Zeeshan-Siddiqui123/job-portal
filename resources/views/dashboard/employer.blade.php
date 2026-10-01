@extends('layouts.app')

@section('title', 'Employer Dashboard | ' . $portalSettings->site_name)

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow" style="margin-bottom: 4px;"><i class="fas fa-building me-1"></i> Employer Portal</p>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">{{ Auth::user()->company_name ?? Auth::user()->name }} Dashboard</h1>
        <p>Manage your job listings and review the applicant recruitment pipeline.</p>
    </div>
    <div>
        <a href="{{ route('jobs.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Post New Job</a>
    </div>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Posted Jobs</div>
        <div class="stat-num" style="color: var(--primary);">{{ $totalPosted }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Total Applications</div>
        <div class="stat-num">{{ $totalApps }}</div>
    </div>
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Shortlisted Candidates</div>
        <div class="stat-num" style="color: var(--success);">{{ $shortlistedApps }}</div>
    </div>
</div>

<!-- In-app notifications box -->
<x-notifications :notifications="$notifications" />

<!-- Posted Jobs -->
<div class="dash-card">
    <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
        <i class="fas fa-briefcase me-2" style="color: var(--primary);"></i> Active Job Listings
    </h2>

    @if($postedJobs->isEmpty())
    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
        <p style="margin-bottom: 12px;">No active job listings yet.</p>
        <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">Create your first listing</a>
    </div>
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
                    <td><span class="job-type-badge">{{ $job->type }}</span></td>
                    <td><strong style="color: var(--primary);">{{ $job->applications_count }}</strong></td>
                    <td>
                        <span class="badge-status status-{{ $job->status }}">
                            {{ $job->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-outline btn-sm">Edit</a>
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
    <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
        <i class="fas fa-users me-2" style="color: var(--primary);"></i> Candidate Applications Pipeline
    </h2>

    @if($applications->isEmpty())
    <p style="color: var(--text-muted); padding: 1rem 0; margin: 0;">No applications received yet.</p>
    @else
    <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Applied Job</th>
                    <th>Resume / Portfolio</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr>
                    <td>
                        <strong style="color: var(--text-main);">{{ $app->jobSeeker->name }}</strong><br>
                        <span style="font-size: 0.8rem; color: var(--text-sub);">{{ $app->jobSeeker->email }}</span>
                        <div style="margin-top: 6px;"><x-application-details :application="$app" /></div>
                    </td>
                    <td><strong>{{ $app->job->title }}</strong></td>
                    <td>
                        @if($app->resume_url)
                        <a href="{{ $app->resume_url }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-external-link-alt me-1"></i> View Resume
                        </a>
                        @else
                        <span style="color: var(--text-sub); font-size: 0.82rem;">Not provided</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status status-{{ $app->status }}">
                            {{ $app->status }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('applications.status', $app->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center;">
                            @csrf
                            <x-custom-dropdown class="custom-dropdown--compact">
                            <select name="status" class="form-control" aria-label="Application status">
                                <option value="Pending" {{ $app->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Shortlisted" {{ $app->status == 'Shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                <option value="Hired" {{ $app->status == 'Hired' ? 'selected' : '' }}>Hired</option>
                                <option value="Rejected" {{ $app->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            </x-custom-dropdown>
                            <button type="submit" class="btn btn-outline btn-sm">Update</button>
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
