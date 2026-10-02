<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobPortal | Career Platform')</title>

    <!-- Meta Tags -->
    <meta name="description" content="Professional Job Board Portal - Search, Post, and Apply for Jobs online with multi-role access for Job Seekers, Employers, and Admins.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @yield('styles')
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-dropdown.css') }}">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="app-shell">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-backdrop" aria-hidden="true"></div>

        <!-- Left Vertical Sidebar Navigation -->
        <aside class="app-sidebar" aria-label="Main sidebar navigation">
            <div>
                <!-- Brand Header -->
                <div class="sidebar-header">
                    <a href="{{ route('jobs.index') }}" class="logo">
                        <div class="logo-box"><i class="fas fa-briefcase"></i></div>
                        <span>{{ $portalSettings->site_name }}</span>
                    </a>
                </div>

                <!-- Nav Groups -->
                <nav class="sidebar-nav">
                    <div>
                        <div class="sidebar-group-title">Main Menu</div>
                        <ul class="sidebar-menu">
                            <li>
                                <a href="{{ route('jobs.index') }}" class="sidebar-link {{ request()->routeIs('jobs.index') || request()->routeIs('home') ? 'active' : '' }}">
                                    <i class="fas fa-search"></i>
                                    <span>Find Jobs</span>
                                </a>
                            </li>

                            @auth
                            <li>
                                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-columns"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>

                            @if(Auth::user()->isEmployer() || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('jobs.create') }}" class="sidebar-link {{ request()->routeIs('jobs.create') ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Post a Job</span>
                                </a>
                            </li>
                            @endif

                            <li>
                                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                    <i class="fas fa-bell"></i>
                                    <span>Notifications</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('profile.show') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-circle"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>
                            @endauth
                        </ul>
                    </div>

                    @auth
                    @if(Auth::user()->isAdmin())
                    <div>
                        <div class="sidebar-group-title">Administration</div>
                        <ul class="sidebar-menu">
                            <li>
                                <a href="{{ route('admin.jobs.index') }}" class="sidebar-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                                    <i class="fas fa-briefcase"></i>
                                    <span>Manage Jobs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i>
                                    <span>Manage Users</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.settings.edit') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                    <i class="fas fa-cog"></i>
                                    <span>System Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                    @endauth
                </nav>
            </div>

            <!-- Sidebar User / Auth Footer -->
            <div class="sidebar-footer">
                @auth
                <div class="sidebar-user-box">
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                        <div class="sidebar-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="sidebar-user-meta">
                            <div class="sidebar-user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                            <span class="role-badge role-{{ Auth::user()->role }}" style="font-size: 0.68rem; padding: 2px 7px;">
                                {{ str_replace('_', ' ', Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" title="Logout" style="min-height: 32px; padding: 4px 8px; color: var(--text-sub);">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
                @else
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('login') }}" class="btn btn-outline" style="width: 100%;"><i class="fas fa-sign-in-alt me-1"></i> Sign In</a>
                    @if($portalSettings->registration_open)
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;"><i class="fas fa-user-plus me-1"></i> Register</a>
                    @endif
                </div>
                @endauth
            </div>
        </aside>

        <!-- Right Main Application Canvas -->
        <div class="app-content">
            <!-- App Topbar -->
            <header class="app-topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <button type="button" class="sidebar-toggle-btn" data-sidebar-toggle aria-label="Toggle navigation menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">
                        {{ $portalSettings->site_name }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    @auth
                        @if(Auth::user()->isEmployer() || Auth::user()->isAdmin())
                        <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Post a Job
                        </a>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline btn-sm" title="Notifications" style="padding: 6px 11px;">
                            <i class="fas fa-bell"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Sign In</a>
                        @if($portalSettings->registration_open)
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                        @endif
                    @endauth
                </div>
            </header>

            <!-- Main Page Content -->
            <main id="main-content" tabindex="-1">
                <div class="container">
                    @if(session('success'))
                    <div class="flash-alert flash-success" role="status" data-success-alert>
                        <i class="fas fa-check-circle fa-lg" aria-hidden="true"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="flash-alert flash-error" role="alert">
                        <i class="fas fa-exclamation-triangle fa-lg" aria-hidden="true"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="flash-alert flash-error" role="alert">
                        <i class="fas fa-exclamation-circle fa-lg" aria-hidden="true"></i>
                        <ul style="padding-left: 1.25rem; margin: 0;">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer>
                <div class="container footer-flex">
                    <div>
                        <strong style="color: var(--text-main); font-size: 0.95rem;">{{ $portalSettings->site_name }} - Career Platform</strong>
                        @if($portalSettings->support_email)
                        <p style="margin-top: 4px;"><a href="mailto:{{ $portalSettings->support_email }}">{{ $portalSettings->support_email }}</a></p>
                        @endif
                        <p style="margin-top: 4px; font-size: 0.8rem;">Hunarmand Punjab Advanced PHP Laravel Web Development (Batch 3 Final Project)</p>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-sub);">
                        &copy; {{ date('Y') }} Zeeshan Ahmed Siddiq. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/portal.js') }}" defer></script>
    @yield('scripts')
</body>
</html>
