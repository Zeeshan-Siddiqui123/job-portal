@props(['application'])
<button class="btn btn-outline btn-sm" type="button" data-dialog-open="application-{{ $application->id }}" aria-haspopup="dialog">
    <i class="fas fa-file-alt me-1"></i> View Application
</button>
<dialog class="application-dialog" id="application-{{ $application->id }}" aria-labelledby="application-title-{{ $application->id }}">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
        <h2 id="application-title-{{ $application->id }}" style="font-size: 1.25rem; font-weight: 700; margin: 0;">Application Details</h2>
        <button type="button" class="btn btn-outline btn-sm" data-dialog-close aria-label="Close application details" autofocus>
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div style="margin-bottom: 18px;">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px;">{{ $application->jobSeeker->name }}</h3>
        <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">{{ $application->jobSeeker->email }}</p>
    </div>

    <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 14px 18px; margin-bottom: 20px;">
        <dl style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin: 0;">
            <div>
                <dt style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 2px;">Job Position</dt>
                <dd style="font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin: 0;">{{ $application->job->title }}</dd>
            </div>
            <div>
                <dt style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 2px;">Applied on</dt>
                <dd style="font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin: 0;">{{ $application->created_at->format('d M Y, h:i A') }}</dd>
            </div>
            <div>
                <dt style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-sub); margin-bottom: 2px;">Current Status</dt>
                <dd style="margin: 0;"><span class="badge-status status-{{ $application->status }}">{{ $application->status }}</span></dd>
            </div>
        </dl>
    </div>

    <div style="margin-bottom: 24px;">
        <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Cover Letter</h3>
        <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: var(--radius); padding: 14px; font-size: 0.9rem; line-height: 1.6; color: var(--text-muted); white-space: pre-wrap;">{{ $application->cover_letter ?: 'No cover letter provided.' }}</div>
    </div>

    <div class="row-actions" style="justify-content: flex-end;">
        <a class="btn btn-outline" href="{{ route('profile.candidate', $application->job_seeker_id) }}">
            <i class="fas fa-user me-1"></i> Candidate Profile
        </a>
        @if($application->resume_url)
        <a class="btn btn-primary" href="{{ $application->resume_url }}" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-external-link-alt me-1"></i> View Resume
        </a>
        @else
        <span class="field-help">No resume provided.</span>
        @endif
    </div>
</dialog>
