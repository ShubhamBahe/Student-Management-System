<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    {{-- Page Header --}}
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">About Us</h1>
        <nav aria-label="breadcrumb" class="justify-content-center d-flex">
            <ol class="breadcrumb" style="background:none">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.7)">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">About</li>
            </ol>
        </nav>
    </div>
    {{-- About Content --}}
    <section style="padding:80px 0">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="badge-section">Our Story</span>
                    <h2 class="section-title mt-3">Excellence in Education Since 2009</h2>
                    <p class="text-muted mt-3">Al-Noor Public School was established with a vision to provide quality education that combines modern curriculum with strong Islamic values. Over the years, we have grown to become one of the leading educational institutions in Hyderabad.</p>
                    <p class="text-muted">Our dedicated team of teachers and staff work tirelessly to ensure every student reaches their full potential in academics, character, and life skills.</p>
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div class="fw-bold fs-3 text-primary">500+</div>
                                <small class="text-muted">Students Enrolled</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div class="fw-bold fs-3 text-success">30+</div>
                                <small class="text-muted">Expert Teachers</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div class="fw-bold fs-3 text-warning">15+</div>
                                <small class="text-muted">Years of Excellence</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div class="fw-bold fs-3 text-danger">95%</div>
                                <small class="text-muted">Pass Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:20px;padding:40px;color:#fff">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-bullseye me-2"></i>Our Vision
                        </h5>
                        <p class="opacity-90">To be the leading school that produces well-rounded individuals who excel academically, morally, and socially.</p>
                        <hr style="border-color:rgba(255,255,255,0.2)">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-heart me-2"></i>Our Mission
                        </h5>
                        <p class="opacity-90">To provide quality education in a nurturing environment that fosters intellectual growth, character development, and Islamic values.</p>
                        <hr style="border-color:rgba(255,255,255,0.2)">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-star me-2"></i>Our Values
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Excellence','Integrity','Respect','Innovation','Teamwork','Faith'] as $v)
                                <span style="background:rgba(255,255,255,0.15);padding:4px 12px;border-radius:50px;font-size:0.8rem">{{ $v }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Principal Message --}}
    <section style="background:#f8fafc;padding:80px 0">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">Leadership</span>
                <h2 class="section-title mt-3">Principal's Message</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-card text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold text-white"
                            style="width:80px;height:80px;background:#2563eb;font-size:1.5rem">P</div>
                        <h5 class="fw-bold mb-1" style="color:#1e293b">Dr. Muhammad Rafiq</h5>
                        <p class="text-primary mb-3" style="font-size:0.875rem;font-weight:500">Principal, Al-Noor Public School</p>
                        <p class="text-muted" style="font-style:italic;line-height:1.8">
                            "At Al-Noor Public School, we believe every child has unique potential. Our dedicated teachers work with passion to unlock that potential while instilling strong Islamic values. We are committed to providing an environment where students feel safe, inspired, and motivated to achieve excellence in all areas of life."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Footer section --}}
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>