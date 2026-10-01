@extends('layouts.app')
@section('title', 'Notifications | JobPortal')
@section('content')
<div class="notifications-page">
    <a href="{{ route('dashboard') }}" class="notifications-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to dashboard</a>
    <div class="notifications-page-heading"><h1>Notifications</h1><p>Keep up with your latest applications and job activity.</p></div>
    <x-notifications :notifications="$notifications" :show-all="false" />
</div>
@endsection
