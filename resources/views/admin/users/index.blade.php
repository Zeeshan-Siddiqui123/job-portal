@extends('layouts.app')
@section('title', 'Manage Users | ' . $portalSettings->site_name)
@section('content')
@include('admin.navigation')

<div class="page-heading">
    <div>
        <p class="eyebrow" style="margin-bottom: 4px;">User Directory</p>
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">Manage Users</h1>
        <p>Search accounts, change permissions, and oversee platform access.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i> Add User</a>
</div>

<form class="admin-filters" method="GET" action="{{ route('admin.users.index') }}">
    <input class="form-control" type="search" name="search" aria-label="Search users" placeholder="Search by name or email address..." value="{{ request('search') }}" maxlength="100">
    <x-custom-dropdown>
        <select name="role" aria-label="Filter by role" class="form-control">
            <option value="">All Roles</option>
            @foreach(['admin' => 'Admin', 'employer' => 'Employer', 'job_seeker' => 'Job Seeker'] as $value => $label)
            <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <button class="btn btn-primary"><i class="fas fa-search me-1"></i> Search</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="dash-card">
    <div class="table-scroll" tabindex="0" role="region" aria-label="User accounts">
        <table class="app-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><strong style="color: var(--text-main);">{{ $user->name }}</strong></td>
                    <td style="color: var(--text-muted);">{{ $user->email }}</td>
                    <td>
                        <span class="role-badge role-{{ $user->role }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </td>
                    <td>
                        <div class="row-actions">
                            <a class="btn btn-outline btn-sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user and their jobs, applications and notifications? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger-outline btn-sm">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No users match your search criteria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-links" style="margin-top: 16px;">{{ $users->links() }}</div>
</div>
@endsection
