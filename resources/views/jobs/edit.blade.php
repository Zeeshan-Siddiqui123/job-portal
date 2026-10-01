@extends('layouts.app')

@section('title', 'Edit Job | ' . $job->title)

@section('content')
<div class="form-shell ">
    <div class="form-panel">
        <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.25rem;">Edit Job Listing</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Update listing details or toggle job status.</p>

        <form action="{{ route('jobs.update', $job->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="category_id">Category *</label>
                    <x-custom-dropdown>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $job->category_id) == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    </x-custom-dropdown>
                </div>
                <div class="form-group">
                    <label class="form-label" for="company">Company Name *</label>
                    <input type="text" id="company" name="company" class="form-control" value="{{ old('company', $job->company) }}" required>
                </div>
            </div>

            <div class="form-grid form-grid--three" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $job->location) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="type">Job Type *</label>
                    <x-custom-dropdown>
                    <select id="type" name="type" class="form-control" required>
                        @foreach(['Full-Time', 'Part-Time', 'Remote', 'Contract'] as $t)
                        <option value="{{ $t }}" @selected(old('type', $job->type) == $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    </x-custom-dropdown>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Job Status *</label>
                    <x-custom-dropdown>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Open" @selected(old('status', $job->status) === 'Open')>Open</option>
                        <option value="Closed" @selected(old('status', $job->status) === 'Closed')>Closed</option>
                    </select>
                    </x-custom-dropdown>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="salary_range">Salary Range</label>
                <input type="text" id="salary_range" name="salary_range" class="form-control" value="{{ old('salary_range', $job->salary_range) }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="experience_level">Experience Level *</label>
                <input type="text" id="experience_level" name="experience_level" class="form-control" value="{{ old('experience_level', $job->experience_level) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Job Description *</label>
                <textarea id="description" name="description" rows="5" class="form-control" required>{{ old('description', $job->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="requirements">Key Requirements</label>
                <textarea id="requirements" name="requirements" rows="4" class="form-control">{{ old('requirements', $job->requirements) }}</textarea>
            </div>

            <div class="form-group">
                <label class="setting-toggle"><input type="checkbox" name="featured" value="1" @checked(old('featured', $job->featured))> Mark as Featured Job Listing</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 1rem;">
                <i class="fas fa-save me-1"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
