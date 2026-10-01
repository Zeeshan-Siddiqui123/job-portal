<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\Notification;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    /**
     * Submit application for a job.
     */
    public function apply(Request $request, $jobId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to submit your application.');
        }

        abort_unless(Auth::user()->isJobSeeker(), 403, 'Only job seekers can apply for jobs.');
        abort_unless(PortalSetting::current()->applications_open, 403, 'Applications are currently paused.');

        $validated = $request->validate([
            'cover_letter' => 'required|string|min:15',
            'resume_url' => 'nullable|url|max:255',
        ]);

        DB::transaction(function () use ($jobId, $validated): void {
            $job = JobListing::lockForUpdate()->findOrFail($jobId);
            if ($job->status !== 'Open') {
                throw ValidationException::withMessages(['application' => 'This job is closed and no longer accepts applications.']);
            }
            if ($job->applications()->where('job_seeker_id', Auth::id())->exists()) {
                throw ValidationException::withMessages(['application' => 'You have already applied for this job.']);
            }
            Application::create([
                'job_id' => $job->id,
                'job_seeker_id' => Auth::id(),
                'cover_letter' => $validated['cover_letter'],
                'resume_url' => $validated['resume_url'] ?? Auth::user()->resume_link,
                'status' => 'Pending',
            ]);
            Notification::create([
                'user_id' => $job->employer_id,
                'title' => 'New Application Received',
                'message' => Auth::user()->name.' applied for '.$job->title,
                'link' => route('dashboard'),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Your application has been submitted successfully!');
    }

    /**
     * Job owner updates candidate application status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Shortlisted,Rejected,Hired',
        ]);

        DB::transaction(function () use ($request, $id): void {
            $application = Application::with('job')->lockForUpdate()->findOrFail($id);
            abort_unless(
                (Auth::user()->isAdmin() || Auth::user()->isEmployer())
                && Auth::id() === $application->job->employer_id,
                403
            );
            if ($application->status === $request->status) {
                return;
            }
            $application->update(['status' => $request->status]);
            Notification::create([
                'user_id' => $application->job_seeker_id,
                'title' => 'Application Status Update',
                'message' => 'Your application for "'.$application->job->title.'" was updated to: '.$request->status,
                'link' => route('dashboard'),
            ]);
        });

        return back()->with('success', 'Candidate status updated to ' . $request->status);
    }
}
