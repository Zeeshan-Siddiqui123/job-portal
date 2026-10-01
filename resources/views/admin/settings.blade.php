@extends('layouts.app')
@section('title', 'System Settings | JobPortal')
@section('content')
@include('admin.navigation')
<div class="form-shell"><div class="form-panel">
    <div class="page-heading"><div><h1>System settings</h1><p>Manage the portal identity and availability of new activity.</p></div></div>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label" for="site_name">Site name</label><input id="site_name" name="site_name" class="form-control" required maxlength="80" value="{{ old('site_name', $settings->site_name) }}"></div>
        <div class="form-group"><label class="form-label" for="support_email">Support email (optional)</label><input type="email" id="support_email" name="support_email" class="form-control" maxlength="150" value="{{ old('support_email', $settings->support_email) }}"><p class="field-help">Displayed in the footer.</p></div>
        @foreach(['registration_open' => 'Allow new account registrations', 'job_posting_open' => 'Allow new job postings', 'applications_open' => 'Allow new job applications'] as $key => $label)
        <div class="form-group">
            <input type="hidden" name="{{ $key }}" value="0">
            <label class="setting-toggle"><input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings->$key))> {{ $label }}</label>
        </div>
        @endforeach
        <p class="field-help">Pausing new activity keeps existing accounts, job listings and applications available.</p>
        <button class="btn btn-primary" style="margin-top: 1rem;">Save settings</button>
    </form>
</div></div>
@endsection
