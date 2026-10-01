<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => 'nullable|string|max:100', 'role' => 'nullable|in:admin,employer,job_seeker']);
        $users = User::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => 'job_seeker'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validated($request));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        DB::transaction(function () use ($request, $user, $data): void {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::lockForUpdate()->findOrFail($user->id);
            if ($user->role !== $data['role']) {
                if ($user->id === $request->user()->id || ($user->isAdmin() && $admins->count() <= 1)) {
                    throw ValidationException::withMessages(['role' => 'You cannot change your own admin role or remove the last administrator.']);
                }
                if (($data['role'] === 'job_seeker' && $user->postedJobs()->exists()) || ($data['role'] !== 'job_seeker' && $user->applications()->exists())) {
                    throw ValidationException::withMessages(['role' => 'This user has jobs or applications that require their current role.']);
                }
            }
            $user->update($data);
        });

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user): void {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user = User::lockForUpdate()->findOrFail($user->id);
            if ($user->id === $request->user()->id || ($user->isAdmin() && $admins->count() <= 1)) {
                throw ValidationException::withMessages(['user' => 'You cannot delete your own account or the last administrator.']);
            }
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'User and their related records deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user?->id)],
            'role' => 'required|in:admin,employer,job_seeker',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
