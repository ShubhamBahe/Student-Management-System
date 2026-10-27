<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('school_name', 'Al-Noor Public School') }} — Home</title>
    <link rel="icon" type="image/png" href="https://ui-avatars.com/api/?name=SMS&background=2563eb&color=fff&size=64&bold=true&font-size=0.4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --dark: #1e293b; }
        body { font-family: 'Inter', sans-serif; }
        /* Navbar */
        .navbar { background: #1e293b; padding: 16px 0; }
        .navbar-brand { font-size: 1.2rem; font-weight: 700; color: #fff !important; }
        .nav-link { color: rgba(255,255,255,0.8) !important; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: #fff !important; }
        .btn-admission { background: #2563eb; color: #fff !important; border-radius: 8px; padding: 8px 20px; font-weight: 600; }
        .btn-admission:hover { background: #1d4ed8; }
        /* Hero */
        .hero {
            background: linear-gradient(135deg, rgba(30,41,59,0.92) 0%, rgba(37,99,235,0.88) 100%),
                        url('https://picsum.photos/1600/900?random=1');
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            top: -200px; right: -100px;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -150px; left: -50px;
        }
        .hero h1 { font-size: 3rem; font-weight: 800; color: #fff; line-height: 1.2; }
        .hero p  { font-size: 1.1rem; color: rgba(255,255,255,0.8); }
        .hero-badge { background: rgba(255,255,255,0.15); color: #fff; border-radius: 50px; padding: 6px 16px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 20px; }
        /* Stats Bar */
        .stats-bar {
            background: linear-gradient(135deg, rgba(37,99,235,0.95), rgba(29,78,216,0.95)),
                        url('https://picsum.photos/1600/400?random=2');
            padding: 30px 0;
        }
        .stat-item { text-align: center; color: #fff; }
        .stat-item .num { font-size: 2rem; font-weight: 800; }
        .stat-item .label { font-size: 0.875rem; opacity: 0.85; }
        /* Section */
        section { padding: 80px 0; }
        .section-title { font-size: 2rem; font-weight: 700; color: #1e293b; }
        .section-subtitle { font-size: 0.95rem; color: #64748b; max-width: 500px; }
        .badge-section { background: #dbeafe; color: #1d4ed8; padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        /* Feature Cards */
        .feature-card { background: #fff; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; transition: all 0.3s; height: 100%; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #2563eb; }
        .feature-icon { width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 16px; }
        /* Teacher Cards */
        .teacher-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s; }
        .teacher-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .teacher-img { width: 100%; height: 200px; object-fit: cover; }
        .teacher-info { padding: 20px; }
        /* News Cards */
        .news-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s; }
        .news-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        /* Testimonials */
        .testimonial-card { background: #fff; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; }
        /* Admission Banner */
        .admission-banner { background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 20px; padding: 60px; color: #fff; }
        /* Contact */
        .contact-info-item { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
        .contact-icon { width: 50px; height: 50px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 1.1rem; flex-shrink: 0; }
        /* Footer */
        footer { background: #1e293b; color: rgba(255,255,255,0.7); padding: 60px 0 20px; }
        footer h6 { color: #fff; font-weight: 600; margin-bottom: 16px; }
        footer a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; display: block; margin-bottom: 8px; }
        footer a:hover { color: #fff; }
    </style>
</head>
<body>
    {{-- NAVBAR --}}
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
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
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
                            Login
                        </a>
                    @endauth
                    <a href="{{ route('admission') }}" class="btn btn-sm btn-admission">Apply Now</a>
                </div>
            </div>
        </div>
    </nav>
    {{-- HERO --}}
    <section class="hero">
        <div class="container position-relative" style="z-index:1">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="hero-badge">
                        <i class="fas fa-star me-1"></i>Best School in Hyderabad
                    </span>
                    <h1>Shaping Tomorrow's<br><span style="color:#60a5fa">Leaders Today</span></h1>
                    <p class="mt-3 mb-4">{{ setting('school_tagline', 'Education is the key to success') }}. We provide quality education with modern facilities and experienced teachers.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admission') }}" class="btn btn-light fw-600 px-4 py-3" style="border-radius:10px;font-weight:600">
                            <i class="fas fa-user-plus me-2 text-primary"></i>Apply for Admission
                        </a>
                        <a href="{{ route('about') }}" class="btn btn-outline-light px-4 py-3" style="border-radius:10px">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                    <div class="d-flex gap-4 mt-5 flex-wrap">
                        <div style="color:rgba(255,255,255,0.8)">
                            <div style="font-size:1.8rem;font-weight:800;color:#fff">500+</div>
                            <div style="font-size:0.8rem">Students Enrolled</div>
                        </div>
                        <div style="color:rgba(255,255,255,0.8)">
                            <div style="font-size:1.8rem;font-weight:800;color:#fff">30+</div>
                            <div style="font-size:0.8rem">Expert Teachers</div>
                        </div>
                        <div style="color:rgba(255,255,255,0.8)">
                            <div style="font-size:1.8rem;font-weight:800;color:#fff">15+</div>
                            <div style="font-size:0.8rem">Years of Excellence</div>
                        </div>
                        <div style="color:rgba(255,255,255,0.8)">
                            <div style="font-size:1.8rem;font-weight:800;color:#fff">95%</div>
                            <div style="font-size:0.8rem">Pass Rate</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div style="background:rgba(255,255,255,0.1);border-radius:24px;padding:40px;backdrop-filter:blur(10px)">
                        <i class="fas fa-graduation-cap" style="font-size:8rem;color:rgba(255,255,255,0.9)"></i>
                        <div style="color:#fff;font-size:1.2rem;font-weight:600;margin-top:16px">Quality Education</div>
                        <div style="color:rgba(255,255,255,0.7);font-size:0.875rem">Building bright futures since 2009</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- STATS BAR --}}
    <div class="stats-bar">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="num">500+</div>
                        <div class="label">
                            <i class="fas fa-users me-1"></i>Students
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="num">30+</div>
                        <div class="label">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Teachers
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="num">10+</div>
                        <div class="label">
                            <i class="fas fa-school me-1"></i>Classes
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="num">15+</div>
                        <div class="label">
                            <i class="fas fa-trophy me-1"></i>Awards Won
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- WHY CHOOSE US --}}
    <section style="background:#f8fafc">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">Why Choose Us</span>
                <h2 class="section-title mt-3">Excellence in Every Aspect</h2>
                <p class="section-subtitle mx-auto mt-2">We provide the best learning environment for your child's growth and development.</p>
            </div>
            <div class="row g-4">
                @php
                    $features = [
                        ['icon' => 'fas fa-book-open', 'color' => '#dbeafe', 'icolor' => '#2563eb', 'title' => 'Quality Education', 'desc' => 'Modern curriculum designed to meet international standards with focus on critical thinking.'],
                        ['icon' => 'fas fa-chalkboard-teacher', 'color' => '#dcfce7', 'icolor' => '#16a34a', 'title' => 'Expert Teachers', 'desc' => 'Highly qualified and experienced teachers dedicated to student success.'],
                        ['icon' => 'fas fa-flask', 'color' => '#fef9c3', 'icolor' => '#ca8a04', 'title' => 'Modern Labs', 'desc' => 'State-of-the-art computer and science labs for practical learning.'],
                        ['icon' => 'fas fa-shield-alt', 'color' => '#fce7f3', 'icolor' => '#db2777', 'title' => 'Safe Environment', 'desc' => 'Secure campus with CCTV and dedicated staff for student safety.'],
                        ['icon' => 'fas fa-futbol', 'color' => '#ffedd5', 'icolor' => '#ea580c', 'title' => 'Sports & Activities', 'desc' => 'Comprehensive sports program and extracurricular activities.'],
                        ['icon' => 'fas fa-mosque', 'color' => '#e0e7ff', 'icolor' => '#4f46e5', 'title' => 'Islamic Values', 'desc' => 'Strong focus on Islamic education and moral character development.'],
                    ];
                @endphp
                @foreach($features as $f)
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon" style="background:{{ $f['color'] }}">
                                <i class="{{ $f['icon'] }}" style="color:{{ $f['icolor'] }}"></i>
                            </div>
                            <h6 class="fw-bold mb-2" style="color:#1e293b">{{ $f['title'] }}</h6>
                            <p class="text-muted mb-0" style="font-size:0.875rem">{{ $f['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- OUR TEACHERS --}}
    <section>
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">Our Team</span>
                <h2 class="section-title mt-3">Meet Our Teachers</h2>
                <p class="section-subtitle mx-auto mt-2">Our dedicated educators bring passion and expertise to every classroom.</p>
            </div>
            <div class="row g-4">
                @forelse($teachers as $teacher)
                    <div class="col-md-3">
                        <div class="teacher-card text-center">
                            <img src="{{ $teacher->user->photo_url }}"
                                alt="{{ $teacher->user->name }}"
                                style="width:100%;height:220px;object-fit:cover"
                                onerror="this.src='https://picsum.photos/300/300?random={{ $loop->index + 20 }}'">
                            <div style="padding:20px">
                                <h6 class="fw-bold mb-1" style="color:#1e293b">{{ $teacher->user->name }}</h6>
                                <p class="text-primary mb-1" style="font-size:0.8rem;font-weight:500">
                                    {{ $teacher->specialization ?? 'Teacher' }}
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.78rem">{{ $teacher->qualification }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No teachers found</div>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('public.teachers') }}" class="btn btn-outline-primary px-4"> View All Teachers
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    {{-- NEWS --}}
    @if($news->count() > 0)
        <section style="background:#f8fafc">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge-section">Latest Updates</span>
                    <h2 class="section-title mt-3">News & Announcements</h2>
                </div>
                <div class="row g-4">
                @forelse($news as $item)
                        <div class="col-md-4">
                            <div class="news-card h-100">
                                <div style="height:200px;overflow:hidden;border-radius:16px 16px 0 0">
                                    <img src="https://picsum.photos/seed/home{{ $loop->index + 1 }}/600/400"
                                        alt="{{ $item->title }}"
                                        style="width:100%;height:200px;object-fit:cover;transition:transform 0.3s"
                                        onmouseover="this.style.transform='scale(1.05)'"
                                        onmouseout="this.style.transform='scale(1)'">
                                </div>
                                <div class="p-4">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                                    </small>
                                    <h6 class="fw-bold mt-2 mb-2" style="color:#1e293b">{{ $item->title }}</h6>
                                    <p class="text-muted mb-3" style="font-size:0.875rem">
                                        {{ Str::limit($item->content, 100) }}
                                    </p>
                                    <a href="{{ route('news.detail', $item->slug) }}"
                                    class="btn btn-sm btn-outline-primary">
                                        Read More <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">No news found</div>
                    @endforelse
                </div>  
            </div>
        </section>
    @endif
    {{-- EVENTS --}}
    @if($events->count() > 0)
        <section>
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge-section">Upcoming</span>
                    <h2 class="section-title mt-3">School Events</h2>
                </div>
                <div class="row g-4">
                    @foreach($events as $event)
                    <div class="col-md-6">
                        <div class="d-flex gap-3 p-4 rounded-3 border"
                            style="background:#fff;transition:all 0.2s"
                            onmouseover="this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)'"
                            onmouseout="this.style.boxShadow='none'">
                            <div style="background:#dbeafe;border-radius:12px;padding:16px;
                                        text-align:center;min-width:70px;flex-shrink:0">
                                <div style="font-size:1.5rem;font-weight:800;color:#2563eb">
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
                                </div>
                                <div style="font-size:0.75rem;color:#64748b;font-weight:600">
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M') }}
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color:#1e293b">{{ $event->title }}</h6>
                                <p class="text-muted mb-1" style="font-size:0.875rem">
                                    {{ Str::limit($event->description, 80) }}
                                </p>
                                <small>
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                    {{ $event->location }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- TESTIMONIALS --}}
    @if($testimonials->count() > 0)
        <section style="background:#f8fafc">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge-section">Testimonials</span>
                    <h2 class="section-title mt-3">What Parents Say</h2>
                </div>
                <div class="row g-4">
                    @foreach($testimonials as $t)
                    <div class="col-md-6">
                        <div class="testimonial-card">
                            <div class="mb-3" style="color:#f59e0b">
                                @for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor
                            </div>
                            <p class="text-muted mb-3">"{{ $t->message }}"</p>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                    style="width:40px;height:40px;background:#2563eb;font-size:0.875rem">
                                    {{ strtoupper(substr($t->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:0.875rem;color:#1e293b">{{ $t->name }}</div>
                                    <small class="text-muted">{{ $t->role }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- GALLERY --}}
    <section style="padding:80px 0">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">Our Campus</span>
                <h2 class="section-title mt-3">School Gallery</h2>
                <p class="section-subtitle mx-auto mt-2">A glimpse of our beautiful school campus and activities.</p>
            </div>
            <div class="row g-3">
                @php
                    $galleryImages = [
                        ['url' => 'https://picsum.photos/600/400?random=11', 'title' => 'Modern Classrooms'],
                        ['url' => 'https://picsum.photos/600/400?random=12', 'title' => 'School Library'],
                        ['url' => 'https://picsum.photos/600/400?random=13', 'title' => 'Computer Lab'],
                        ['url' => 'https://picsum.photos/600/400?random=14', 'title' => 'Sports Ground'],
                        ['url' => 'https://picsum.photos/600/400?random=15', 'title' => 'Science Lab'],
                        ['url' => 'https://picsum.photos/600/400?random=16', 'title' => 'Student Activities'],
                    ];
                @endphp
                @foreach($galleryImages as $img)
                    <div class="col-md-4 col-6">
                        <div style="border-radius:12px;overflow:hidden;position:relative;height:200px;cursor:pointer"
                            onmouseover="this.querySelector('.overlay').style.opacity='1'"
                            onmouseout="this.querySelector('.overlay').style.opacity='0'">
                            <img src="{{ $img['url'] }}"
                                alt="{{ $img['title'] }}"
                                style="width:100%;height:200px;object-fit:cover;transition:transform 0.3s"
                                onmouseover="this.style.transform='scale(1.05)'"
                                onmouseout="this.style.transform='scale(1)'">
                            <div class="overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;
                                        background:rgba(37,99,235,0.7);display:flex;align-items:center;
                                        justify-content:center;opacity:0;transition:opacity 0.3s">
                                <div style="color:#fff;text-align:center">
                                    <i class="fas fa-search-plus fa-2x mb-2 d-block"></i>
                                    <span style="font-weight:600;font-size:0.875rem">{{ $img['title'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('gallery') }}" class="btn btn-outline-primary px-4"> View Full Gallery 
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    {{-- ADMISSION BANNER --}}
    <section>
        <div class="container">
            <div class="admission-banner text-center">
                <h2 class="fw-bold mb-3">Admissions Open 2025-2026!</h2>
                <p class="mb-4 opacity-90">Limited seats available. Apply now to secure your child's future.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('admission') }}" class="btn btn-light fw-bold px-5 py-3" style="border-radius:10px;color:#2563eb">
                        <i class="fas fa-user-plus me-2"></i>Apply for Admission
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-light px-5 py-3" style="border-radius:10px">
                        <i class="fas fa-phone me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
    {{-- CONTACT --}}
    <section style="background:#f8fafc">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <span class="badge-section">Get In Touch</span>
                    <h2 class="section-title mt-3">Contact Us</h2>
                    <p class="text-muted mt-2 mb-4">Have questions? We are here to help you.</p>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Address</div>
                            <small class="text-muted">{{ setting('school_address', 'Hyderabad, Sindh, Pakistan') }}</small>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Phone</div>
                            <small class="text-muted">{{ setting('school_phone', '+92-300-0000000') }}</small>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Email</div>
                            <small class="text-muted">{{ setting('school_email', 'info@school.com') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="page-card">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('contact.send') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Your Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Muhammad Ali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="ali@example.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control" placeholder="How can we help?">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Write your message here..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3" style="border-radius:10px;font-weight:600">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <h6>
                        <i class="fas fa-graduation-cap me-2" style="color:#2563eb"></i>{{ setting('school_name') }}
                    </h6>
                    <p style="font-size:0.875rem;color:rgba(255,255,255,0.5)">Providing quality education and shaping young minds since 2009.</p>
                </div>
                <div class="col-lg-2">
                    <h6>Quick Links</h6>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('about') }}">About Us</a>
                    <a href="{{ route('admission') }}">Admission</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
                <div class="col-lg-2">
                    <h6>Academics</h6>
                    <a href="{{ route('public.teachers') }}">Teachers</a>
                    <a href="{{ route('fee.structure') }}">Fee Structure</a>
                    <a href="{{ route('events') }}">Events</a>
                    <a href="{{ route('news') }}">News</a>
                </div>
                <div class="col-lg-4">
                    <h6>Contact Info</h6>
                    <p style="font-size:0.875rem;color:rgba(255,255,255,0.5)">
                        <i class="fas fa-map-marker-alt me-2"></i>{{ setting('school_address') }}<br>
                        <i class="fas fa-phone me-2 mt-2 d-inline-block"></i>{{ setting('school_phone') }}<br>
                        <i class="fas fa-envelope me-2 mt-2 d-inline-block"></i>{{ setting('school_email') }}
                    </p>
                </div>
            </div>
            <hr style="border-color:rgba(255,255,255,0.1)">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:0.8rem">
                <span>© {{ date('Y') }} {{ setting('school_name') }}. All rights reserved.</span>
                <div class="d-flex gap-3">
                    <a href="{{ route('privacy') }}" style="color:rgba(255,255,255,0.5)">Privacy Policy</a>
                    <a href="{{ route('terms') }}" style="color:rgba(255,255,255,0.5)">Terms</a>
                    <a href="{{ route('login') }}" style="color:rgba(255,255,255,0.5)">Admin Login</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>