@extends('layouts.app')
@section('title', 'Manage Jobs | ' . $portalSettings->site_name)
@section('content')
@include('admin.navigation')

<div class="page-heading">
    <div>
        <p class="eyebrow" style="margin-bottom: 4px;">Job Inventory</p>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">Manage Jobs</h1>
        <p>Browse, filter, and manage all {{ $jobs->total() }} platform job postings.</p>
    </div>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Post a Job</a>
</div>

<form class="admin-filters job-management-filters" method="GET" action="{{ route('admin.jobs.index') }}">
    <input class="form-control" type="search" name="search" aria-label="Search jobs" placeholder="Title, company, or location..." value="{{ request('search') }}" maxlength="150">
    <x-custom-dropdown>
        <select name="status" class="form-control" aria-label="Filter by job status">
            <option value="">All Statuses</option>
            @foreach(['Open', 'Closed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <x-custom-dropdown>
        <select name="category" class="form-control" aria-label="Filter by category">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <x-custom-dropdown>
        <select name="type" class="form-control" aria-label="Filter by job type">
            <option value="">All Job Types</option>
            @foreach(['Full-Time', 'Part-Time', 'Contract', 'Remote'] as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <button class="btn btn-primary"><i class="fas fa-search me-1"></i> Search</button>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="dash-card">
    <div class="table-scroll" role="region" aria-label="All job listings" tabindex="0">
        <table class="app-table">
            <thead>
                <tr>
                    <th>Job Title & Organization</th>
                    <th>Posted By</th>
                    <th>Category & Type</th>
                    <th>Status</th>
                    <th>Applications</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td>
                        <strong><a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a></strong><br>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $job->company }} &bull; {{ $job->location }}</span>
                    </td>
                    <td style="color: var(--text-main);">{{ $job->employer->name }}</td>
                    <td>
                        <span style="font-weight: 500;">{{ $job->category->name }}</span><br>
                        <span class="job-type-badge" style="margin-top: 3px;">{{ $job->type }}</span>
                    </td>
                    <td>
                        <span class="badge-status status-{{ $job->status }}">{{ $job->status }}</span>
                    </td>
                    <td><strong style="color: var(--primary);">{{ $job->applications_count }}</strong></td>
                    <td>
                        <div class="row-actions">
                            <a class="btn btn-outline btn-sm" href="{{ route('jobs.edit', $job->id) }}">Edit</a>
                            <form method="POST" action="{{ route('jobs.destroy', $job->id) }}" onsubmit="return confirm('Delete this job and its applications? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger-outline btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No jobs match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-links" style="margin-top: 16px;">{{ $jobs->links() }}</div>
</div>
@endsection
