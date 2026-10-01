@extends('layouts.app')
@section('title', ($user->exists ? 'Edit User' : 'Add User').' | JobPortal')
@section('content')
@include('admin.navigation')
<div class="form-shell"><div class="form-panel">
    <div class="page-heading"><h1>{{ $user->exists ? 'Edit user' : 'Add user' }}</h1></div>
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif
        <div class="form-group"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100"></div>
        <div class="form-group"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="150"></div>
        <div class="form-group">
            <label class="form-label" for="role">Role</label>
            <x-custom-dropdown><select class="form-control" id="role" name="role" required>
                @foreach(['job_seeker' => 'Job Seeker', 'employer' => 'Employer', 'admin' => 'Admin'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select></x-custom-dropdown>
            <p class="field-help">Your own administrator role is protected. Users with jobs or applications must retain a compatible role.</p>
        </div>
        <div class="form-grid">
            <div class="form-group"><label class="form-label" for="password">{{ $user->exists ? 'New password (optional)' : 'Password' }}</label><input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" @required(!$user->exists)></div>
            <div class="form-group"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(!$user->exists)></div>
        </div>
        <div class="row-actions"><button class="btn btn-primary">Save user</button><a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a></div>
    </form>
</div></div>
@endsection
