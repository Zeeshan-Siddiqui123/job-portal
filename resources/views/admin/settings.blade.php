@extends('layouts.app')
@section('title', 'System Settings | ' . $portalSettings->site_name)
@section('content')
@include('admin.navigation')

<div class="form-shell">
    <div class="form-panel">
        <div style="margin-bottom: 24px;">
            <p class="eyebrow" style="margin-bottom: 4px;">Configuration</p>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;">System Settings</h1>
            <p>Manage portal branding, support contacts, and module availability.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="site_name">Portal Name</label>
                <input id="site_name" name="site_name" class="form-control" required maxlength="80" value="{{ old('site_name', $settings->site_name) }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="support_email">Support Email Address</label>
                <input type="email" id="support_email" name="support_email" class="form-control" maxlength="150" value="{{ old('support_email', $settings->support_email) }}" placeholder="support@example.com">
                <p class="field-help">Displayed in the footer for candidate and employer inquiries.</p>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin: 24px 0 16px;">
                <h2 style="font-size: 1rem; font-weight: 700; margin-bottom: 14px;">Platform Activity Toggles</h2>
                @foreach(['registration_open' => 'Allow new account registrations', 'job_posting_open' => 'Allow new job postings', 'applications_open' => 'Allow new job applications'] as $key => $label)
                <div class="form-group" style="margin-bottom: 12px;">
                    <input type="hidden" name="{{ $key }}" value="0">
                    <label class="setting-toggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-main); font-size: 0.9rem;">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings->$key))>
                        <span>{{ $label }}</span>
                    </label>
                </div>
                @endforeach
                <p class="field-help" style="margin-top: 8px;">Pausing new activity keeps existing accounts, listings, and applications accessible for browsing.</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
