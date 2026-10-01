@extends('layouts.app')
@section('title', 'Manage Jobs | JobPortal')
@section('content')
@include('admin.navigation')
<div class="page-heading">
    <div><h1>Manage jobs</h1><p>Browse and manage all {{ $jobs->total() }} matching listings.</p></div>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary">Post a job</a>
</div>
<form class="admin-filters job-management-filters" method="GET" action="{{ route('admin.jobs.index') }}">
    <input class="form-control" type="search" name="search" aria-label="Search jobs" placeholder="Title, company or location" value="{{ request('search') }}" maxlength="150">
    <x-custom-dropdown>
        <select name="status" class="form-control" aria-label="Filter by job status">
            <option value="">All statuses</option>
            @foreach(['Open', 'Closed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <x-custom-dropdown>
        <select name="category" class="form-control" aria-label="Filter by category">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <x-custom-dropdown>
        <select name="type" class="form-control" aria-label="Filter by job type">
            <option value="">All job types</option>
            @foreach(['Full-Time', 'Part-Time', 'Contract', 'Remote'] as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <button class="btn btn-primary">Search</button>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline">Reset</a>
</form>
<div class="dash-card">
    <div class="table-scroll" role="region" aria-label="All job listings" tabindex="0">
        <table class="app-table">
            <thead><tr><th>Job</th><th>Posted by</th><th>Category / Type</th><th>Status</th><th>Applications</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td><a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a><br><small>{{ $job->company }} · {{ $job->location }}</small></td>
                    <td>{{ $job->employer->name }}</td>
                    <td>{{ $job->category->name }}<br><small>{{ $job->type }}</small></td>
                    <td>{{ $job->status }}</td>
                    <td>{{ $job->applications_count }}</td>
                    <td><div class="row-actions">
                        <a class="btn btn-outline" href="{{ route('jobs.edit', $job->id) }}">Edit</a>
                        <form method="POST" action="{{ route('jobs.destroy', $job->id) }}" onsubmit="return confirm('Delete this job and its applications? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline" style="color: var(--danger);">Delete</button>
                        </form>
                    </div></td>
                </tr>
                @empty
                <tr><td colspan="6">No jobs match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-links">{{ $jobs->links() }}</div>
</div>
@endsection
