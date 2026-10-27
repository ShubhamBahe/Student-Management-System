<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-graduation-cap me-2" style="color:#2563eb"></i>
            {{ setting('school_name', 'Al-Noor Public School') }}
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="fas fa-bars text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'text-white' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('public.teachers') }}">Teachers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('events') }}">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('news') }}">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('fee.structure') }}">Fee Structure</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('faq') }}">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                @endauth
                <a href="{{ route('admission') }}" class="btn btn-sm btn-admission">Apply Now</a>
            </div>
        </div>
    </div>
</nav>