@extends('layouts.app')

@section('title', 'Admin Platform Analytics | JobPortal')

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

    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
@include('admin.navigation')

<x-notifications :notifications="$notifications" />

<section class="dash-card" aria-label="Job Applications">
    <h2 style="font-size: 1.2rem;">Job Applications</h2>
    <p class="field-help">View all applications. You can update statuses only for jobs you posted.</p>
    @if($applications->isEmpty())
        <p>No applications received yet.</p>
    @else
        <div class="table-scroll" role="region" aria-label="Job applicants" tabindex="0">
            <table class="app-table">
                <thead><tr><th>Candidate</th><th>Applied Job</th><th>Resume / Link</th><th>Application Status</th><th>Change Status</th></tr></thead>
                <tbody>
                    @foreach($applications as $application)
                    <tr>
                        <td><strong>{{ $application->jobSeeker->name }}</strong><br>{{ $application->jobSeeker->email }}<br><x-application-details :application="$application" /></td>
                        <td><a href="{{ route('jobs.show', $application->job_id) }}">{{ $application->job->title }}</a><br><small>{{ $application->job->company }}</small></td>
                        <td>
                            @if($application->resume_url)
                                <a href="{{ $application->resume_url }}" target="_blank" rel="noopener noreferrer">View Resume</a>
                            @else
                                <span class="field-help">Not provided</span>
                            @endif
                        </td>
                        <td>{{ $application->status }}</td>
                        <td>
                            @if(auth()->id() === $application->job->employer_id)
                            <form action="{{ route('applications.status', $application->id) }}" method="POST" class="row-actions">
                                @csrf
                                <x-custom-dropdown class="custom-dropdown--compact">
                                    <select name="status" class="form-control" aria-label="Application status for {{ $application->jobSeeker->name }}">
                                        @foreach(['Pending', 'Shortlisted', 'Hired', 'Rejected'] as $status)
                                            <option value="{{ $status }}" @selected($application->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </x-custom-dropdown>
                                <button type="submit" class="btn btn-outline">Update</button>
                            </form>
                            @else
                                <span class="field-help">View only</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $applications->links() }}
    @endif
</section>

<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: var(--danger); font-weight: 600; text-transform: uppercase;"><i class="fas fa-user-shield me-1"></i> Global Administration</div>
    <h1 style="font-size: 2.2rem; font-weight: 800;">System Analytics & Control Center</h1>
    <p style="color: var(--text-muted);">Overview of job board performance, system users, and job listings.</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">TOTAL USERS</div>
        <div class="stat-num" style="color: var(--primary-light);">{{ $totalUsers }}</div>
        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $totalEmployers }} Employers • {{ $totalJobSeekers }} Candidates</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">TOTAL JOB LISTINGS</div>
        <div class="stat-num" style="color: var(--accent);">{{ $totalJobs }}</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">APPLICATIONS SUBMITTED</div>
        <div class="stat-num" style="color: var(--text-muted);">{{ $totalApplications }}</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.82rem; color: var(--text-sub);">TOTAL HIRED</div>
        <div class="stat-num" style="color: var(--text-muted);">{{ $hiredCount }}</div>
    </div>
</div>

<div class="form-grid" style="gap: 2rem;">
    <!-- System Jobs Manager -->
    <div class="dash-card">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-briefcase me-2" style="color: var(--primary-light);"></i> System Job Listings</h3>
        
        <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
<table class="app-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Employer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentJobs as $rJob)
                <tr>
                    <td><strong><a href="{{ route('jobs.show', $rJob->id) }}">{{ $rJob->title }}</a></strong></td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $rJob->company }}</td>
                    <td>
                        <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; background: var(--bg-subtle); color: var(--text-muted);">
                            {{ $rJob->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('jobs.edit', $rJob->id) }}" style="color: var(--primary-light); font-size: 0.82rem; margin-right: 0.5rem;">Edit</a>
                        <form action="{{ route('jobs.destroy', $rJob->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete job?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 0.82rem;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>
    </div>

    <!-- Registered Users Manager -->
    <div class="dash-card">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem;"><i class="fas fa-users me-2" style="color: var(--accent);"></i> Registered System Users</h3>

        <div class="table-scroll" role="region" aria-label="Dashboard records" tabindex="0">
<table class="app-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentUsers as $rUser)
                <tr>
                    <td><strong>{{ $rUser->name }}</strong></td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $rUser->email }}</td>
                    <td>
                        <span class="role-badge role-{{ $rUser->role }}">
                            {{ str_replace('_', ' ', $rUser->role) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>
    </div>
</div>

@endsection
