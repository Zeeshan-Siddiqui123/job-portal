<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JobPortal | LinkedIn-Style Job Board')</title>

    <!-- Meta Tags -->
    <meta name="description" content="LinkedIn-Style Job Board Portal - Search, Post, and Apply for Jobs online with multi-role access for Job Seekers, Employers, and Admins.">

    <!-- Google Fonts -->
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


    <!-- Main Navigation Bar -->
    <nav class="navbar" aria-label="Main navigation">
        <div class="container nav-container">
            <a href="{{ route('jobs.index') }}" class="logo">
                <div class="logo-box"><i class="fas fa-briefcase"></i></div>
                <span>{{ $portalSettings->site_name }}</span>
            </a>

            <button class="nav-toggle btn btn-outline" type="button" aria-expanded="false" aria-controls="primary-navigation account-navigation"><i class="fas fa-bars" aria-hidden="true"></i> Menu</button>
            <ul class="nav-menu" id="primary-navigation">
                <li><a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.index') ? 'active' : '' }}"><i class="fas fa-search"></i> Jobs</a></li>
                
                @auth
                    <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-columns"></i> Dashboard</a></li>

                    @if(Auth::user()->isEmployer() || Auth::user()->isAdmin())
                    <li><a href="{{ route('jobs.create') }}" class="nav-link" style="color: var(--accent);"><i class="fas fa-plus-circle"></i> Post a Job</a></li>
                    @endif

                    <li><a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="fas fa-user-circle"></i> Profile</a></li>
                @endauth
            </ul>

            <div class="nav-account" id="account-navigation">
                @auth
                    <span class="role-badge role-{{ Auth::user()->role }}">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>
                    
                    <div class="account-details">
                        <span style="font-weight: 600; font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.82rem;" title="Logout">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                    @if($portalSettings->registration_open)
                    <a href="{{ route('register') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main id="main-content" tabindex="-1">
        <div class="container">
            @if(session('success'))
            <div class="flash-alert flash-success">
                <i class="fas fa-check-circle fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="flash-alert flash-error">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            @if($errors->any())
            <div class="flash-alert flash-error" role="alert">
                <ul style="padding-left: 1rem;">
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
                <strong style="color: var(--text-main); font-size: 1.05rem;">{{ $portalSettings->site_name }} - Career Platform</strong>
                @if($portalSettings->support_email)
                <p><a href="mailto:{{ $portalSettings->support_email }}">{{ $portalSettings->support_email }}</a></p>
                @endif
                <p style="margin-top: 0.25rem;">Built for Hunarmand Punjab Advanced PHP Laravel Web Development (Batch 3 Final Project)</p>
            </div>
            <div>
                &copy; {{ date('Y') }} Zeeshan Ahmed Siddiq. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/custom-dropdown.js') }}" defer></script>
    <script src="{{ asset('js/portal.js') }}" defer></script>
    @yield('scripts')
</body>
</html>
