<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Teachers — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Our Teachers</h1>
        <nav aria-label="breadcrumb" class="justify-content-center d-flex">
            <ol class="breadcrumb" style="background:none">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.7)">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">Teachers</li>
            </ol>
        </nav>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">Our Team</span>
                <h2 class="section-title mt-3">Meet Our Expert Teachers</h2>
                <p class="section-subtitle mx-auto mt-2">Highly qualified and passionate educators dedicated to student success.</p>
            </div>
            <div class="row g-4">
                @forelse($teachers as $teacher)
                    <div class="col-md-3">
                        <div class="teacher-card text-center">
                            <img src="{{ $teacher->user->photo_url }}" alt="{{ $teacher->user->name }}"
                                style="width:100%;height:200px;object-fit:cover">
                            <div style="padding:20px">
                                <h6 class="fw-bold mb-1" style="color:#1e293b">{{ $teacher->user->name }}</h6>
                                <p class="text-primary mb-1" style="font-size:0.8rem;font-weight:500">{{ $teacher->specialization ?? 'Teacher' }}</p>
                                <p class="text-muted mb-2" style="font-size:0.78rem">{{ $teacher->qualification }}</p>
                                <span class="badge badge-active">{{ ucfirst($teacher->user->gender ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3 d-block"></i>No teachers found
                    </div>
                @endforelse
            </div>
            {{ $teachers->links() }}
        </div>
    </section>
    {{-- Footer section --}}
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>