@extends('layouts.app')

@section('title', 'Sign In | ' . $portalSettings->site_name)

@section('content')
<div class="form-shell auth-shell">
    <div class="form-panel">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 48px; height: 48px; background: var(--primary-tint); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; border-radius: var(--radius); margin-bottom: 12px; border: 1px solid #BFDBFE;">
                <i class="fas fa-lock"></i>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;">Welcome Back</h1>
            <p style="font-size: 0.9rem;">Sign in to access your dashboard, applications, and saved jobs.</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                @error('email')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                @error('password')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 0.875rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-main);">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me on this device</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; min-height: 44px; font-size: 0.95rem;">
                <i class="fas fa-sign-in-alt me-1"></i> Sign In
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">
                Don't have an account? <a href="{{ route('register') }}" style="font-weight: 600;">Create an account</a>
            </p>
        </div>
    </div>
</div>
@endsection
