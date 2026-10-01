<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:150',
            'status' => 'nullable|in:Open,Closed',
            'category' => 'nullable|integer|exists:categories,id',
            'type' => 'nullable|in:Full-Time,Part-Time,Contract,Remote',
        ]);

        $query = JobListing::with(['employer', 'category'])->withCount('applications');
        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('company', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }
        foreach (['status' => 'status', 'category' => 'category_id', 'type' => 'type'] as $filter => $column) {
            if (! empty($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }

        return view('admin.jobs.index', [
            'jobs' => $query->latest()->orderByDesc('id')->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
