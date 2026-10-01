@extends('layouts.app')

@section('title', $job->title . ' at ' . $job->company . ' | ' . $portalSettings->site_name)

@section('content')

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('jobs.index') }}" class="btn btn-outline btn-sm" style="display: inline-flex;">
        <i class="fas fa-arrow-left"></i> Back to Job Listings
    </a>
</div>

<article class="job-detail-card">
    <div class="job-header" style="align-items: center; margin-bottom: 24px;">
        <div style="display: flex; gap: 18px; align-items: center;">
            <div class="company-logo-placeholder" style="width: 58px; height: 58px; font-size: 1.5rem;">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">{{ $job->title }}</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0; display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                    <strong style="color: var(--text-main);">{{ $job->company }}</strong>
                    <span style="color: var(--border-color);">&bull;</span>
                    <span><i class="fas fa-map-marker-alt me-1" style="color: var(--primary);"></i> {{ $job->location }}</span>
                    @if($job->category)
                    <span style="color: var(--border-color);">&bull;</span>
                    <span style="color: var(--text-sub);">{{ $job->category->name }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @auth
                @if(Auth::user()->isEmployer() || Auth::user()->isAdmin())
                    @if(Auth::id() === $job->employer_id || Auth::user()->isAdmin())
                    <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-outline"><i class="fas fa-edit"></i> Edit Listing</a>
                    <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this job listing?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger-outline"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                    @endif
                @else
                    @if($hasApplied)
                    <span class="role-badge applied-label" style="padding: 8px 14px; font-size: 0.85rem;">
                        <i class="fas fa-check-circle me-1"></i> Application Submitted
                    </span>
                    @elseif($job->status !== 'Open' || !$applicationsOpen)
                    <button class="btn btn-outline" disabled>{{ $job->status !== 'Open' ? 'Applications closed' : 'Applications paused' }}</button>
                    @else
                    <button class="btn btn-primary" onclick="document.getElementById('applyModal').style.display='flex'">
                        <i class="fas fa-paper-plane me-1"></i> Apply Now
                    </button>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-1"></i> Sign In to Apply
                </a>
            @endauth
        </div>
    </div>

    <!-- Clean Metadata Bar -->
    <div class="detail-grid">
        <div class="detail-box">
            <div class="detail-label">Job Type</div>
            <div class="detail-val" style="color: var(--primary);">{{ $job->type }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Experience</div>
            <div class="detail-val">{{ $job->experience_level }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Salary Range</div>
            <div class="detail-val">{{ $job->salary_range ?? 'Competitive' }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Posted Date</div>
            <div class="detail-val">{{ $job->created_at->format('M d, Y') }}</div>
        </div>
    </div>

    <!-- Description -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-main);">Job Overview</h2>
        <div style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; white-space: pre-line;">{{ $job->description }}</div>
    </div>

    <!-- Requirements -->
    @if($job->requirements)
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-main);">Candidate Requirements</h2>
        <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.92rem; line-height: 1.75; white-space: pre-line;">{{ $job->requirements }}</div>
    </div>
    @endif
</article>

<!-- Related Jobs -->
@if(!$relatedJobs->isEmpty())
<section style="margin-top: 2.5rem;" aria-label="Similar Jobs">
    <h2 style="font-size: 1.3rem; margin-bottom: 1.25rem; color: var(--text-main);">Similar Jobs You Might Like</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        @foreach($relatedJobs as $rJob)
        <article class="job-card" style="margin-bottom: 0;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
                <a href="{{ route('jobs.show', $rJob->id) }}">{{ $rJob->title }}</a>
            </h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">{{ $rJob->company }} &bull; {{ $rJob->location }}</p>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                <span class="job-type-badge">{{ $rJob->type }}</span>
                <a href="{{ route('jobs.show', $rJob->id) }}" class="btn btn-outline btn-sm">View Details</a>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif

<!-- Application Modal -->
<div class="modal-overlay" id="applyModal" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; margin: 0;">Submit Application</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">{{ $job->title }} at {{ $job->company }}</p>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('applyModal').style.display='none'" aria-label="Close dialog">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('applications.apply', $job->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="cover_letter">Cover Letter / Note *</label>
                <textarea id="cover_letter" name="cover_letter" rows="5" class="form-control" required placeholder="Explain why you are a great match for this position..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="resume_url">Resume / Portfolio Link</label>
                <input type="url" id="resume_url" name="resume_url" class="form-control" value="{{ Auth::user()->resume_link ?? '' }}" placeholder="https://github.com/username or LinkedIn link">
                <p class="field-help">Provide a link to your CV, portfolio, or LinkedIn profile.</p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('applyModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
