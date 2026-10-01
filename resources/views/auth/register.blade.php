@extends('layouts.app')

@section('title', 'Register Account | JobPortal')

@section('content')
<div class="form-shell auth-shell">
    <div class="form-panel">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 50px; height: 50px; background: var(--bg-subtle); color: var(--accent); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 700;">Create an Account</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Join as a Job Seeker or Employer</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Role Selector -->
            <div class="form-group">
                <label class="form-label">I want to join as a *</label>
                <div class="form-grid" style="gap: 1rem;">
                    <label style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 1rem; cursor: pointer; text-align: center; display: block;" id="roleSeekerBox">
                        <input type="radio" name="role" value="job_seeker" checked onchange="toggleRoleFields('job_seeker')" style="margin-bottom: 0.5rem;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: var(--primary-light);"><i class="fas fa-user-tie me-1"></i> Job Seeker</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">Apply for jobs & showcase profile</div>
                    </label>

                    <label style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 1rem; cursor: pointer; text-align: center; display: block;" id="roleEmployerBox">
                        <input type="radio" name="role" value="employer" onchange="toggleRoleFields('employer')" style="margin-bottom: 0.5rem;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: var(--accent);"><i class="fas fa-building me-1"></i> Employer</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">Post jobs & hire candidates</div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Zeeshan Ahmed Siddiq">
                @error('name')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="your.email@example.com">
                @error('email')
                <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="companyField" style="display: none;">
                <label class="form-label" for="company_name">Company / Organization Name</label>
                <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. TechCorp Solutions">
            </div>

            <div class="form-group">
                <label class="form-label" for="headline">Professional Headline</label>
                <input type="text" id="headline" name="headline" class="form-control" value="{{ old('headline') }}" placeholder="e.g. Senior Laravel Developer / Hiring Manager">
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="••••••••">
                </div>
            </div>
            @error('password')
            <span style="color: var(--danger); font-size: 0.8rem; margin-bottom: 1rem; display: block;">{{ $message }}</span>
            @enderror

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 0.5rem;">
                <i class="fas fa-user-plus me-1"></i> Complete Registration
            </button>
        </form>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="font-size: 0.88rem; color: var(--text-muted);">
                Already have an account? <a href="{{ route('login') }}" style="color: var(--primary-light); font-weight: 600;">Sign In</a>
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
