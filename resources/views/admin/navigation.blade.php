<nav class="admin-actions" aria-label="Administration">
    <a class="btn btn-sm {{ request()->routeIs('dashboard') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('dashboard') }}"><i class="fas fa-chart-pie me-1"></i> Overview</a>
    <a class="btn btn-sm {{ request()->routeIs('admin.users.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.users.index') }}"><i class="fas fa-users me-1"></i> Manage users</a>
    <a class="btn btn-sm {{ request()->routeIs('admin.jobs.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.jobs.index') }}"><i class="fas fa-briefcase me-1"></i> Manage jobs</a>
    <a class="btn btn-sm {{ request()->routeIs('admin.settings.*') ? 'btn-primary' : 'btn-outline' }}" href="{{ route('admin.settings.edit') }}"><i class="fas fa-cog me-1"></i> System settings</a>
</nav>
