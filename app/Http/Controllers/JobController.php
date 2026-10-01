<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobListing;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display searchable job listings with filters.
     */
    public function index(Request $request)
    {
        $query = JobListing::with(['employer', 'category'])->where('status', 'Open');
        if (Auth::check()) {
            $query->withExists(['applications as has_applied' => fn ($applications) => $applications->where('job_seeker_id', Auth::id())]);
        }

        // Keyword Search (Title, Description, Company)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('company', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by Location
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Job Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $jobs = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::withCount('jobs')->get();
        $locations = JobListing::select('location')->distinct()->pluck('location');

        return view('jobs.index', compact('jobs', 'categories', 'locations'));
    }

    /**
     * Display individual job details.
     */
    public function show($id)
    {
        $job = JobListing::with(['employer', 'category', 'applications'])->findOrFail($id);

        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = $job->applications()->where('job_seeker_id', Auth::id())->exists();
        }

        $relatedJobs = JobListing::where('category_id', $job->category_id)
            ->where('status', 'Open')
            ->where('id', '!=', $job->id)
            ->take(3)
            ->get();

        return view('jobs.show', compact('job', 'hasApplied', 'relatedJobs'))->with('applicationsOpen', PortalSetting::current()->applications_open);
    }

    /**
     * Show job creation form for Employers and Admin.
     */
    public function create()
    {
        if (! PortalSetting::current()->job_posting_open) {
            return redirect()->route('dashboard')->with('error', 'New job posting is currently paused.');
        }
        if (! Auth::check() || (Auth::user()->isJobSeeker())) {
            return redirect()->route('login')->with('error', 'Only Employers and Admins can post jobs.');
        }

        $categories = Category::all();

        return view('jobs.create', compact('categories'));
    }

    /**
     * Store new job listing in database.
     */
    public function store(Request $request)
    {
        abort_unless(PortalSetting::current()->job_posting_open, 403, 'New job posting is currently paused.');
        if (! Auth::check() || (Auth::user()->isJobSeeker())) {
            return redirect()->route('login')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'company' => 'required|string|max:150',
            'location' => 'required|string|max:100',
            'type' => 'required|in:Full-Time,Part-Time,Contract,Remote',
            'salary_range' => 'nullable|string|max:100',
            'experience_level' => 'required|string|max:50',
            'description' => 'required|string|min:20',
            'requirements' => 'nullable|string',
        ]);

        $validated['employer_id'] = Auth::id();
        $validated['status'] = 'Open';
        $validated['featured'] = $request->has('featured') ? true : false;

        $job = JobListing::create($validated);

        return redirect()->route('jobs.show', $job->id)->with('success', 'Job listing published successfully!');
    }

    /**
     * Edit job listing.
     */
    public function edit($id)
    {
        $job = JobListing::findOrFail($id);

        if (Auth::id() !== $job->employer_id && ! Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $categories = Category::all();

        return view('jobs.edit', compact('job', 'categories'));
    }

    /**
     * Update job listing.
     */
    public function update(Request $request, $id)
    {
        $job = JobListing::findOrFail($id);

        if (Auth::id() !== $job->employer_id && ! Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'company' => 'required|string|max:150',
            'location' => 'required|string|max:100',
            'type' => 'required|in:Full-Time,Part-Time,Contract,Remote',
            'salary_range' => 'nullable|string|max:100',
            'experience_level' => 'required|string|max:50',
            'description' => 'required|string|min:20',
            'requirements' => 'nullable|string',
            'status' => 'required|in:Open,Closed',
        ]);

        $validated['featured'] = $request->has('featured') ? true : false;
        $job->update($validated);

        return redirect()->route('jobs.show', $job->id)->with('success', 'Job listing updated successfully!');
    }

    /**
     * Delete job listing.
     */
    public function destroy($id)
    {
        $job = JobListing::findOrFail($id);

        if (Auth::id() !== $job->employer_id && ! Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $job->delete();

        return redirect()->route('dashboard')->with('success', 'Job listing deleted.');
    }
}
