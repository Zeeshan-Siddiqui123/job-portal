@extends('layouts.app')

@section('title', $job->title . ' at ' . $job->company . ' | JobPortal')

@section('styles')
<style>
    .job-detail-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }

    .job-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        padding: 1.25rem;
        margin-bottom: 2rem;
    }

    .detail-box {
        text-align: center;
    }

    .detail-label {
        font-size: 0.78rem;
        color: var(--text-sub);
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .detail-val {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main);
    }

    /* Application Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.35);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        max-width: 600px;
        width: 100%;
        padding: 2rem;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection

@section('content')

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('jobs.index') }}" style="color: var(--text-muted); font-size: 0.9rem;">
        <i class="fas fa-arrow-left me-1"></i> Back to Job Listings
    </a>
</div>

<div class="job-detail-card">
    <div class="job-header-flex">
        <div style="display: flex; gap: 1.25rem; align-items: center;">
            <div style="width: 64px; height: 64px; background: var(--bg-subtle); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--primary-light); font-size: 1.75rem;">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $job->title }}</h1>
                <p style="color: var(--text-muted); font-size: 1rem; margin-top: 0.25rem;">
                    <strong style="color: var(--text-main);">{{ $job->company }}</strong> • <i class="fas fa-map-marker-alt" style="color: var(--primary-light);"></i> {{ $job->location }}
                </p>
            </div>
        </div>

        <div>
            @auth
                @if(Auth::user()->isEmployer() || Auth::user()->isAdmin())
                    @if(Auth::id() === $job->employer_id || Auth::user()->isAdmin())
                    <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-outline me-2"><i class="fas fa-edit"></i> Edit Listing</a>
                    <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this job listing?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline" style="color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                    @endif
                @else
                    @if($hasApplied)
                    <button class="btn btn-accent" disabled style="opacity: 0.85;">
                        <i class="fas fa-check-circle me-1"></i> Application Submitted
                    </button>
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
                    <i class="fas fa-sign-in-alt me-1"></i> Login to Apply
                </a>
            @endauth
        </div>
    </div>

    <!-- Metadata Grid -->
    <div class="detail-grid">
        <div class="detail-box">
            <div class="detail-label">JOB TYPE</div>
            <div class="detail-val" style="color: var(--primary-light);">{{ $job->type }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">EXPERIENCE</div>
            <div class="detail-val">{{ $job->experience_level }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">SALARY RANGE</div>
            <div class="detail-val" style="color: var(--accent);">{{ $job->salary_range ?? 'Competitive' }}</div>
        </div>
        <div class="detail-box">
            <div class="detail-label">POSTED DATE</div>
            <div class="detail-val">{{ $job->created_at->format('M d, Y') }}</div>
        </div>
    </div>

    <!-- Description & Requirements -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.3rem; margin-bottom: 1rem; color: var(--text-main);">Job Description</h3>
        <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.7; white-space: pre-line;">{{ $job->description }}</p>
    </div>

    @if($job->requirements)
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.3rem; margin-bottom: 1rem; color: var(--text-main);">Candidate Requirements</h3>
        <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 1.5rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.8; white-space: pre-line;">
            {{ $job->requirements }}
        </div>
    </div>
    @endif
</div>

<!-- Related Jobs -->
@if(!$relatedJobs->isEmpty())
<div style="margin-top: 3rem;">
    <h3 style="font-size: 1.4rem; margin-bottom: 1.25rem; color: var(--text-main);">Similar Jobs You Might Like</h3>
    <div class="related-grid" style="gap: 1.5rem;">
        @foreach($relatedJobs as $rJob)
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.25rem;">
            <h4 style="font-size: 1.1rem; color: var(--text-main);"><a href="{{ route('jobs.show', $rJob->id) }}">{{ $rJob->title }}</a></h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">{{ $rJob->company }} • {{ $rJob->location }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Application Modal -->
<div class="modal-overlay" id="applyModal">
    <div class="modal-card">
        <button class="close-modal" onclick="document.getElementById('applyModal').style.display='none'">&times;</button>
        
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-main);">Submit Job Application</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Position: {{ $job->title }} at {{ $job->company }}</p>

        <form action="{{ route('applications.apply', $job->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="cover_letter">Cover Letter / Pitch *</label>
                <textarea id="cover_letter" name="cover_letter" rows="5" class="form-control" required placeholder="Explain why you are the ideal candidate for this role..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="resume_url">Resume / Portfolio Link</label>
                <input type="url" id="resume_url" name="resume_url" class="form-control" value="{{ Auth::user()->resume_link ?? '' }}" placeholder="https://github.com/username or LinkedIn link">
                <span style="font-size: 0.78rem; color: var(--text-sub); display: block; margin-top: 0.25rem;">Provide a public GitHub, LinkedIn, or Google Drive URL.</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 1rem;">
                <i class="fas fa-paper-plane me-1"></i> Confirm & Submit Application
            </button>
        </form>
    </div>
</div>

@endsection
