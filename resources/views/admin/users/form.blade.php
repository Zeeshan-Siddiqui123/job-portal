@extends('layouts.app')
@section('title', ($user->exists ? 'Edit User' : 'Add User') . ' | ' . $portalSettings->site_name)
@section('content')
@include('admin.navigation')

<div class="form-shell">
    <div class="form-panel">
        <div style="margin-bottom: 24px;">
            <p class="eyebrow" style="margin-bottom: 4px;">User Management</p>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;">{{ $user->exists ? 'Edit User Account' : 'Add New User' }}</h1>
            <p>{{ $user->exists ? 'Update account details and role permissions.' : 'Create a new user account with assigned role.' }}</p>
        </div>

        <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if($user->exists) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100" placeholder="e.g. Jane Doe">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="150" placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Account Role</label>
                <x-custom-dropdown>
                    <select class="form-control" id="role" name="role" required>
                        @foreach(['job_seeker' => 'Job Seeker', 'employer' => 'Employer', 'admin' => 'Admin'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-custom-dropdown>
                <p class="field-help">Your own administrator role is protected. Users with existing jobs or applications must retain a compatible role.</p>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="password">{{ $user->exists ? 'New Password (optional)' : 'Password' }}</label>
                    <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" @required(!$user->exists) placeholder="{{ $user->exists ? 'Leave blank to keep current' : 'At least 8 characters' }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(!$user->exists) placeholder="Repeat password">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save me-1"></i> Save User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
