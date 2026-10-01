@extends('layouts.app')

@section('title', 'Post a New Job | ' . $portalSettings->site_name)

@section('content')
<div class="form-shell">
    <div class="form-panel">
        <div style="margin-bottom: 24px;">
            <p class="eyebrow" style="margin-bottom: 4px;">Recruitment Portal</p>
            <h1 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 6px;">Post a New Job Listing</h1>
            <p>Publish an open opportunity to reach qualified candidates.</p>
        </div>

        <form action="{{ route('jobs.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Senior Backend Engineer (Laravel)">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="category_id">Category *</label>
                    <x-custom-dropdown>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    </x-custom-dropdown>
                </div>
                <div class="form-group">
                    <label class="form-label" for="company">Company Name *</label>
                    <input type="text" id="company" name="company" class="form-control" value="{{ Auth::user()->company_name ?? 'TechCorp' }}" required placeholder="e.g. TechCorp Solutions">
                </div>
            </div>

            <div class="form-grid form-grid--three">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location', 'Lahore, Pakistan') }}" required placeholder="e.g. Lahore, Karachi, Remote">
                </div>
                <div class="form-group">
                    <label class="form-label" for="type">Job Type *</label>
                    <x-custom-dropdown>
                    <select id="type" name="type" class="form-control" required>
                        @foreach(['Full-Time', 'Part-Time', 'Remote', 'Contract'] as $type)
                        <option value="{{ $type }}" @selected(old('type', 'Full-Time') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    </x-custom-dropdown>
                </div>
                <div class="form-group">
                    <label class="form-label" for="experience_level">Experience Level *</label>
                    <x-custom-dropdown>
                    <select id="experience_level" name="experience_level" class="form-control" required>
                        @foreach(['Entry Level', 'Mid Level', 'Senior Level', 'Executive'] as $level)
                        <option value="{{ $level }}" @selected(old('experience_level', 'Mid Level') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                    </x-custom-dropdown>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="salary_range">Salary Range (Optional)</label>
                <input type="text" id="salary_range" name="salary_range" class="form-control" value="{{ old('salary_range') }}" placeholder="e.g. PKR 150,000 - 200,000 / month">
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Job Description *</label>
                <textarea id="description" name="description" rows="5" class="form-control" required placeholder="Provide a detailed overview of responsibilities and daily tasks..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="requirements">Key Requirements & Skills</label>
                <textarea id="requirements" name="requirements" rows="4" class="form-control" placeholder="List required experience, technical tools, and qualifications..."></textarea>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="featured" id="featured" value="1">
                <label for="featured" style="cursor: pointer; font-size: 0.9rem; color: var(--text-main); font-weight: 500;">Mark as Featured Job Listing</label>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-paper-plane me-1"></i> Publish Job Listing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
