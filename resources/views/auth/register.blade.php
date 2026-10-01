@extends('layouts.app')

@section('title', 'Register Account | ' . $portalSettings->site_name)

@section('content')
<div class="form-shell auth-shell">
    <div class="form-panel">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 48px; height: 48px; background: var(--primary-tint); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; border-radius: var(--radius); margin-bottom: 12px; border: 1px solid #BFDBFE;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;">Create an Account</h1>
            <p style="font-size: 0.9rem;">Join as a Job Seeker or Employer to get started.</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Role Selector -->
            <div class="form-group">
                <label class="form-label">I want to join as a *</label>
                <div class="form-grid" style="gap: 12px;">
                    <label style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 14px; cursor: pointer; text-align: center; display: block; transition: all 0.15s ease;" id="roleSeekerBox">
                        <input type="radio" name="role" value="job_seeker" checked onchange="toggleRoleFields('job_seeker')" style="margin-bottom: 6px;">
                        <div style="font-weight: 600; font-size: 0.92rem; color: var(--primary);"><i class="fas fa-user-tie me-1"></i> Job Seeker</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">Apply for jobs & build profile</div>
                    </label>

                    <label style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 14px; cursor: pointer; text-align: center; display: block; transition: all 0.15s ease;" id="roleEmployerBox">
                        <input type="radio" name="role" value="employer" onchange="toggleRoleFields('employer')" style="margin-bottom: 6px;">
                        <div style="font-weight: 600; font-size: 0.92rem; color: var(--text-main);"><i class="fas fa-building me-1"></i> Employer</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">Post jobs & manage applicants</div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Zeeshan Ahmed Siddiq">
                @error('name')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="name@example.com">
                @error('email')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="companyField" style="display: none;">
                <label class="form-label" for="company_name">Company / Organization Name</label>
                <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. TechCorp Solutions">
            </div>

            <div class="form-group">
                <label class="form-label" for="headline">Professional Headline</label>
                <input type="text" id="headline" name="headline" class="form-control" value="{{ old('headline') }}" placeholder="e.g. Senior Full Stack Engineer / Talent Partner">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="At least 6 characters">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>
            </div>
            @error('password')
            <span style="color: var(--danger); font-size: 0.8rem; margin-bottom: 12px; display: block;">{{ $message }}</span>
            @enderror

            <button type="submit" class="btn btn-primary" style="width: 100%; min-height: 44px; font-size: 0.95rem; margin-top: 8px;">
                <i class="fas fa-user-plus me-1"></i> Complete Registration
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">
                Already have an account? <a href="{{ route('login') }}" style="font-weight: 600;">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleRoleFields(role) {
        const companyField = document.getElementById('companyField');
        if (role === 'employer') {
            companyField.style.display = 'block';
        } else {
            companyField.style.display = 'none';
        }
    }
</script>
@endsection
