@extends('layouts.app')

@section('title', 'Browse Job Listings | ' . $portalSettings->site_name)

@section('content')

<!-- Search Banner Header -->
<section class="search-hero" aria-label="Job Search">
    <p class="eyebrow">Discover Opportunities</p>
    <h1>Find work that moves you forward</h1>
    <p>Explore verified openings across engineering, product, design, and operations.</p>

    <form action="{{ route('jobs.index') }}" method="GET" class="search-grid">
        <div>
            <input type="text" name="keyword" class="form-control" aria-label="Job title, skills, or company" placeholder="Job title, skills, or company..." value="{{ request('keyword') }}">
        </div>
        <div>
            <x-custom-dropdown>
            <select name="location" class="form-control" aria-label="Location">
                <option value="">All Locations</option>
                @foreach($locations as $loc)
                <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
            </x-custom-dropdown>
        </div>
        <div>
            <x-custom-dropdown>
            <select name="type" class="form-control" aria-label="Job type">
                <option value="">All Job Types</option>
                <option value="Full-Time" {{ request('type') == 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                <option value="Part-Time" {{ request('type') == 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                <option value="Remote" {{ request('type') == 'Remote' ? 'selected' : '' }}>Remote</option>
                <option value="Contract" {{ request('type') == 'Contract' ? 'selected' : '' }}>Contract</option>
            </select>
            </x-custom-dropdown>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>
</section>

<!-- Job Layout Grid -->
<div class="job-layout">
    <!-- Filters Sidebar -->
    <aside aria-label="Filters">
        <div class="filter-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-sliders-h" style="color: var(--primary); font-size: 0.9rem;"></i> Filters
                </h2>
                @if(request()->anyFilled(['keyword', 'location', 'category', 'type']))
                <a href="{{ route('jobs.index') }}" style="font-size: 0.78rem; color: var(--danger); font-weight: 600;">Reset All</a>
                @endif
            </div>

            <form action="{{ route('jobs.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                @if(request('location'))
                <input type="hidden" name="location" value="{{ request('location') }}">
                @endif
                
                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    @foreach($categories as $cat)
                    <label style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.55rem; font-size: 0.88rem; cursor: pointer; color: var(--text-main);">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                            {{ $cat->name }}
                        </span>
                        <span style="font-size: 0.72rem; color: var(--text-sub); background: var(--bg-subtle); padding: 2px 7px; border-radius: var(--radius-full); border: 1px solid var(--border-color);">{{ $cat->jobs_count }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="filter-group">
                    <div class="filter-title">Job Type</div>
                    @foreach(['Full-Time', 'Part-Time', 'Remote', 'Contract'] as $t)
                    <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 0.55rem; font-size: 0.88rem; cursor: pointer; color: var(--text-main);">
                        <input type="radio" name="type" value="{{ $t }}" {{ request('type') == $t ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                        <span>{{ $t }}</span>
                    </label>
                    @endforeach
                </div>
            </form>
        </div>
    </aside>

    <!-- Job Listings Feed -->
    <section aria-label="Job Results">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Showing <strong>{{ $jobs->total() }}</strong> open position{{ $jobs->total() === 1 ? '' : 's' }}
            </p>
        </div>

        @if($jobs->isEmpty())
        <div class="empty-state">
            <div style="width: 52px; height: 52px; border-radius: var(--radius-full); background: var(--bg-subtle); display: grid; place-items: center; margin: 0 auto 12px; color: var(--text-sub); font-size: 1.25rem;">
                <i class="fas fa-search"></i>
            </div>
            <h3>No jobs found matching your criteria</h3>
            <p style="margin-top: 6px;">Try adjusting your filters, clearing search terms, or checking back soon.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline" style="margin-top: 1rem;">Clear All Filters</a>
        </div>
        @else

        @foreach($jobs as $job)
        <article class="job-card">
            <div class="job-header">
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <div class="company-logo-placeholder">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
                            <a href="{{ route('jobs.show', $job->id) }}" style="color: inherit;">{{ $job->title }}</a>
                        </h3>
                        <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0; display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
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

                <span class="job-type-badge {{ $job->type === 'Remote' ? 'job-type-remote' : '' }}">
                    {{ $job->type }}
                </span>
            </div>

            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 8px 0 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.55;">
                {{ $job->description }}
            </p>

            <div class="job-card-footer">
                <div style="display: flex; align-items: center; gap: 12px; font-size: 0.85rem;">
                    @if($job->salary_range)
                    <span style="color: var(--primary); font-weight: 600;"><i class="fas fa-money-bill-wave me-1"></i> {{ $job->salary_range }}</span>
                    @endif
                    <span style="color: var(--text-sub);"><i class="fas fa-briefcase me-1"></i> {{ $job->experience_level }}</span>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="color: var(--text-sub); font-size: 0.8rem;">Posted {{ $job->created_at->diffForHumans() }}</span>
                    @if($job->has_applied)
                    <span class="role-badge applied-label"><i class="fas fa-check me-1" aria-hidden="true"></i> Applied</span>
                    @else
                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-primary btn-sm">
                        View & Apply <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                    @endif
                </div>
            </div>
        </article>
        @endforeach

        <div style="margin-top: 2rem;">
            {{ $jobs->links() }}
        </div>

        @endif
    </section>
</div>
@endsection
