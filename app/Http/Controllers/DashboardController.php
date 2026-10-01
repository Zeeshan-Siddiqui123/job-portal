<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\Application;
use App\Models\User;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Mark unread notifications
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        if ($user->isAdmin()) {
            $totalUsers = User::count();
            $totalEmployers = User::where('role', 'employer')->count();
            $totalJobSeekers = User::where('role', 'job_seeker')->count();
            $totalJobs = JobListing::count();
            $totalApplications = Application::count();
            $hiredCount = Application::where('status', 'Hired')->count();

            $recentJobs = JobListing::with('employer')->latest()->take(6)->get();
            $recentUsers = User::latest()->take(6)->get();
            $categories = Category::all();
            $applications = Application::with(['job', 'jobSeeker'])->latest()->paginate(15);

            return view('dashboard.admin', compact(
                'totalUsers',
                'totalEmployers',
                'totalJobSeekers',
                'totalJobs',
                'totalApplications',
                'hiredCount',
                'recentJobs',
                'recentUsers',
                'categories',
                'notifications',
                'applications'
            ));
        }

        if ($user->isEmployer()) {
            $postedJobs = JobListing::where('employer_id', $user->id)
                ->withCount('applications')
                ->latest()
                ->get();

            $jobIds = $postedJobs->pluck('id');
            $applications = Application::whereIn('job_id', $jobIds)
                ->with(['job', 'jobSeeker'])
                ->latest()
                ->get();

            $totalPosted = $postedJobs->count();
            $totalApps = $applications->count();
            $shortlistedApps = $applications->where('status', 'Shortlisted')->count();

            return view('dashboard.employer', compact(
                'postedJobs',
                'applications',
                'totalPosted',
                'totalApps',
                'shortlistedApps',
                'notifications'
            ));
        }

        // Default: Job Seeker
        $myApplications = Application::where('job_seeker_id', $user->id)
            ->with(['job.employer', 'job.category'])
            ->latest()
            ->get();

        $stats = [
            'total_applied' => $myApplications->count(),
            'pending' => $myApplications->where('status', 'Pending')->count(),
            'shortlisted' => $myApplications->where('status', 'Shortlisted')->count(),
            'hired' => $myApplications->where('status', 'Hired')->count(),
        ];

        $recommendedJobs = JobListing::where('status', 'Open')
            ->whereNotIn('id', $myApplications->pluck('job_id'))
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.job_seeker', compact(
            'myApplications',
            'stats',
            'recommendedJobs',
            'notifications'
        ));
    }
}
