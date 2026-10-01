@extends('layouts.app')

@section('title', 'Sign In | JobPortal')

@section('content')
<div class="form-shell auth-shell">
    <div class="form-panel">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 50px; height: 50px; background: var(--bg-subtle); color: var(--primary-light); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                <i class="fas fa-lock"></i>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 700;">Welcome Back</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Sign in to manage your jobs & applications</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="your.email@example.com">
                @error('email')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                @error('password')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.85rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                <i class="fas fa-sign-in-alt me-1"></i> Sign In
            </button>
        </form>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="font-size: 0.88rem; color: var(--text-muted);">
                Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary-light); font-weight: 600;">Register Here</a>
            </p>
        </div>


    </div>
</div>
@endsection
