@extends('layouts.app')

@section('title', 'Admin Platform Analytics | ' . $portalSettings->site_name)

@section('content')
@include('admin.navigation')

<div class="page-heading">
    <div>
        <p class="eyebrow" style="margin-bottom: 4px;"><i class="fas fa-shield-alt me-1"></i> Admin Portal</p>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">System Analytics & Control Center</h1>
        <p>Overview of job board performance, registered users, and active job postings.</p>
    </div>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Total Users</div>
        <div class="stat-num" style="color: var(--primary);">{{ $totalUsers }}</div>
        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">{{ $totalEmployers }} Employers &bull; {{ $totalJobSeekers }} Candidates</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Total Job Listings</div>
        <div class="stat-num">{{ $totalJobs }}</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Applications Submitted</div>
        <div class="stat-num">{{ $totalApplications }}</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 6px;">Total Hired</div>
        <div class="stat-num" style="color: var(--success);">{{ $hiredCount }}</div>
    </div>
</div>

<x-notifications :notifications="$notifications" />

<section class="dash-card" aria-label="Job Applications">
    <div style="margin-bottom: 16px;">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 2px;">Job Applications</h2>
        <p class="field-help" style="margin-top: 0;">Review all applications across the platform. Statuses can be modified for jobs you posted.</p>
    </div>

    @if($applications->isEmpty())
        <p style="color: var(--text-muted); padding: 1rem 0; margin: 0;">No applications received yet.</p>
    @else
        <div class="table-scroll" role="region" aria-label="Job applicants" tabindex="0">
            <table class="app-table">
                <thead><tr><th>Candidate</th><th>Applied Job</th><th>Resume / Portfolio</th><th>Status</th><th>Change Status</th></tr></thead>
                <tbody>
                    @foreach($applications as $application)
                    <tr>
                        <td>
                            <strong style="color: var(--text-main);">{{ $application->jobSeeker->name }}</strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-sub);">{{ $application->jobSeeker->email }}</span>
                            <div style="margin-top: 6px;"><x-application-details :application="$application" /></div>
                        </td>
                        <td>
                            <strong><a href="{{ route('jobs.show', $application->job_id) }}">{{ $application->job->title }}</a></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $application->job->company }}</span>
                        </td>
                        <td>
                            @if($application->resume_url)
                                <a href="{{ $application->resume_url }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; font-weight: 600;">
                                    <i class="fas fa-external-link-alt me-1"></i> View Resume
                                </a>
                            @else
                                <span class="field-help">Not provided</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status status-{{ $application->status }}">{{ $application->status }}</span>
                        </td>
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
                                <button type="submit" class="btn btn-outline btn-sm">Update</button>
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
        <div style="margin-top: 16px;">
            {{ $applications->links() }}
        </div>
    @endif
</section>

<div class="form-grid" style="gap: 24px;">
    <!-- System Jobs Manager -->
    <div class="dash-card">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
            <i class="fas fa-briefcase me-2" style="color: var(--primary);"></i> Recent Job Listings
        </h2>
        
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
                            <span class="badge-status status-{{ $rJob->status }}">
                                {{ $rJob->status }}
                            </span>
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('jobs.edit', $rJob->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form action="{{ route('jobs.destroy', $rJob->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete job?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger-outline btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Registered Users Manager -->
    <div class="dash-card">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px;">
            <i class="fas fa-users me-2" style="color: var(--primary);"></i> Recently Registered Users
        </h2>

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
