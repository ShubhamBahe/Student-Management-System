<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">School Events</h1>
        <nav aria-label="breadcrumb" class="justify-content-center d-flex">
            <ol class="breadcrumb" style="background:none">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,0.7)">Home</a></li>
                <li class="breadcrumb-item active text-white">Events</li>
            </ol>
        </nav>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row g-4">
                @forelse($events as $event)
                    <div class="col-md-6">
                        <div class="page-card h-100" style="border-left:4px solid #2563eb">
                            <div class="d-flex gap-4">
                                <div style="background:#dbeafe;border-radius:12px;padding:16px 20px;text-align:center;min-width:80px">
                                    <div style="font-size:2rem;font-weight:800;color:#2563eb;line-height:1">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</div>
                                    <div style="font-size:0.75rem;color:#64748b;font-weight:600">{{ \Carbon\Carbon::parse($event->start_date)->format('M Y') }}</div>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2" style="color:#1e293b">{{ $event->title }}</h5>
                                    <p class="text-muted mb-2" style="font-size:0.875rem">{{ $event->description }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <small>
                                            <i class="fas fa-map-marker-alt text-primary me-1"></i>{{ $event->location }}
                                        </small>
                                        <small>
                                            <i class="fas fa-calendar text-success me-1"></i>{{ \Carbon\Carbon::parse($event->start_date)->format('d M') }} — {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-calendar fa-3x mb-3 d-block"></i>No events found
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $events->links() }}</div>
        </div>
    </section>
    {{-- Footer section --}}
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>