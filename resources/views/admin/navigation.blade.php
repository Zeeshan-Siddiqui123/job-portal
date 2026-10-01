<nav class="admin-actions" aria-label="Administration">
    <a class="btn {{ request()->routeIs('dashboard') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('dashboard') }}">Overview</a>
    <a class="btn {{ request()->routeIs('admin.users.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.users.index') }}">Manage users</a>
    <a class="btn {{ request()->routeIs('admin.jobs.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.jobs.index') }}">Manage jobs</a>
    <a class="btn {{ request()->routeIs('admin.settings.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.settings.edit') }}">System settings</a>
</nav>
