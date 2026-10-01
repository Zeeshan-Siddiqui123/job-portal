@extends('layouts.app')
@section('title', 'Manage Users | JobPortal')
@section('content')
@include('admin.navigation')
<div class="page-heading">
    <div><h1>Manage users</h1><p>Search accounts and manage their access.</p></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
</div>
<form class="admin-filters" method="GET" action="{{ route('admin.users.index') }}">
    <input class="form-control" type="search" name="search" aria-label="Search users" placeholder="Search name or email" value="{{ request('search') }}" maxlength="100">
    <x-custom-dropdown>
        <select name="role" aria-label="Filter by role" class="form-control">
            <option value="">All roles</option>
            @foreach(['admin' => 'Admin', 'employer' => 'Employer', 'job_seeker' => 'Job Seeker'] as $value => $label)
            <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </x-custom-dropdown>
    <button class="btn btn-primary">Search</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Reset</a>
</form>
<div class="dash-card">
    <div class="table-scroll" tabindex="0" role="region" aria-label="User accounts">
        <table class="app-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ str_replace('_', ' ', $user->role) }}</td>
                    <td><div class="row-actions">
                        <a class="btn btn-outline" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user and their jobs, applications and notifications? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline" style="color: var(--danger);">Delete</button>
                        </form>
                        @endif
                    </div></td>
                </tr>
                @empty
                <tr><td colspan="4">No users match your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-links">{{ $users->links() }}</div>
</div>
@endsection
