@extends('layouts.app')
@section('title', 'Notifications | ' . $portalSettings->site_name)
@section('content')
<div class="notifications-page">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Back to dashboard</a>
    </div>
    <div class="page-heading">
        <div>
            <p class="eyebrow" style="margin-bottom: 4px;">Activity Feed</p>
            <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">Notifications</h1>
            <p>Keep up with your latest applications, candidate submissions, and status updates.</p>
        </div>
    </div>
    <x-notifications :notifications="$notifications" :show-all="false" />
</div>
@endsection
