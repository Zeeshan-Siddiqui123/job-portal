@extends('layouts.app')

@section('title', 'Browse Job Listings | JobPortal')

@section('styles')
<style>
    /* Search Header Banner */
    .search-hero {
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        padding: 2.5rem 2rem;
        margin-bottom: 2.5rem;
    }

    .search-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 0.8fr;
        gap: 1rem;
        align-items: center;
    }

    .job-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
    }

    .filter-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        position: sticky;
        top: 90px;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .job-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .job-card:hover {
        border-color: var(--border-color);
        
    }

    .job-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .company-logo-placeholder {
        width: 48px;
        height: 48px;
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-light);
        font-size: 1.25rem;
    }

    .job-type-badge {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        background: var(--bg-subtle);
        color: var(--primary-light);
        border: 1px solid var(--border-color);
    }

    .job-type-remote {
        background: var(--bg-subtle);
        color: var(--text-muted);
        border-color: var(--border-color);
    }

    @media (max-width: 992px) {
        .search-grid {
            grid-template-columns: 1fr;
        }
        .job-layout {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

<!-- Search Banner Header -->
<div class="search-hero">
    <p class="eyebrow">YOUR NEXT CHAPTER</p>
    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">Find work that moves you forward.</h1>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Discover tech, engineering, and digital roles at top companies.</p>

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
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>
</div>

<!-- Job Layout Grid -->
<div class="job-layout">
    <!-- Filters Sidebar -->
    <div>
        <div class="filter-card">
            <h3 style="font-size: 1.1rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-filter me-1" style="color: var(--primary-light);"></i> Filters</span>
                @if(request()->anyFilled(['keyword', 'location', 'category', 'type']))
                <a href="{{ route('jobs.index') }}" style="font-size: 0.78rem; color: var(--danger);">Reset All</a>
                @endif
            </h3>

            <form action="{{ route('jobs.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                
                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    @foreach($categories as $cat)
                    <label style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                        <span>
                            <input type="radio" name="category" value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                            {{ $cat->name }}
                        </span>
                        <span style="font-size: 0.75rem; color: var(--text-sub);">{{ $cat->jobs_count }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="filter-group">
                    <div class="filter-title">Job Type</div>
                    @foreach(['Full-Time', 'Part-Time', 'Remote', 'Contract'] as $t)
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                        <input type="radio" name="type" value="{{ $t }}" {{ request('type') == $t ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()" style="margin-right: 0.5rem;">
                        {{ $t }}
                    </label>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <!-- Job Listings Feed -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Showing <strong>{{ $jobs->total() }}</strong> open position(s)
            </p>
        </div>

        @if($jobs->isEmpty())
        <div class="empty-state">
            <i class="fas fa-search" style="color: var(--text-sub); font-size: 1.75rem; margin-bottom: 0.75rem;"></i>
            <h3>No jobs found matching your criteria.</h3>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Try clearing your filters or searching for another keyword.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline" style="margin-top: 1rem;">Clear All Filters</a>
        </div>
        @else

        @foreach($jobs as $job)
        <div class="job-card">
            <div class="job-header">
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div class="company-logo-placeholder">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main);">
                            <a href="{{ route('jobs.show', $job->id) }}" style="color: inherit;">{{ $job->title }}</a>
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">
                            <strong>{{ $job->company }}</strong> • <i class="fas fa-map-marker-alt" style="color: var(--primary-light);"></i> {{ $job->location }}
                        </p>
                    </div>
                </div>

                <span class="job-type-badge {{ $job->type === 'Remote' ? 'job-type-remote' : '' }}">
                    {{ $job->type }}
                </span>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $job->description }}
            </p>

            <div class="job-card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: auto; font-size: 0.85rem; color: var(--text-sub);">
                <div>
                    @if($job->salary_range)
                    <span style="color: var(--accent); font-weight: 600;"><i class="fas fa-money-bill-wave me-1"></i> {{ $job->salary_range }}</span>
                    @else
                    <span><i class="fas fa-briefcase me-1"></i> {{ $job->experience_level }}</span>
                    @endif
                </div>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span>Posted {{ $job->created_at->diffForHumans() }}</span>
                    @if($job->has_applied)
                    <span class="btn btn-accent applied-label"><i class="fas fa-check" aria-hidden="true"></i> Applied</span>
                    @else
                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">
                        View & Apply <i class="fas fa-arrow-right"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 2rem;">
            {{ $jobs->links() }}
        </div>

        @endif
    </div>
</div>
@endsection
