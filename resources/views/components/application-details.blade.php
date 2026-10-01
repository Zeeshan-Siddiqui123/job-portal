@props(['application'])
<button class="btn btn-outline" type="button" data-dialog-open="application-{{ $application->id }}" aria-haspopup="dialog">View Application</button>
<dialog class="application-dialog" id="application-{{ $application->id }}" aria-labelledby="application-title-{{ $application->id }}">
    <div class="page-heading">
        <h2 id="application-title-{{ $application->id }}">Application details</h2>
        <button type="button" class="btn btn-outline" data-dialog-close aria-label="Close application details" autofocus>Close</button>
    </div>
    <h3>{{ $application->jobSeeker->name }}</h3>
    <p>{{ $application->jobSeeker->email }}</p>
    <dl class="application-facts">
        <dt>Job</dt><dd>{{ $application->job->title }}</dd>
        <dt>Applied on</dt><dd>{{ $application->created_at->format('d M Y, h:i A') }}</dd>
        <dt>Status</dt><dd>{{ $application->status }}</dd>
    </dl>
    <h3>Cover letter</h3>
    <p class="application-cover-letter">{{ $application->cover_letter ?: 'No cover letter provided.' }}</p>
    <div class="row-actions">
        <a class="btn btn-outline" href="{{ route('profile.candidate', $application->job_seeker_id) }}">View candidate profile</a>
        @if($application->resume_url)
        <a class="btn btn-primary" href="{{ $application->resume_url }}" target="_blank" rel="noopener noreferrer">View Resume</a>
        @else
        <span class="field-help">No resume provided.</span>
        @endif
    </div>
</dialog>
