<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">{{ $news->title }}</h1>
        <small class="opacity-70"><i class="fas fa-calendar me-1">
            </i>{{ \Carbon\Carbon::parse($news->published_at)->format('d M Y') }}
        </small>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-card">
                        <p class="text-muted" style="line-height:1.9;font-size:1rem">{{ $news->content }}</p>
                        <hr>
                        <a href="{{ route('news') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to News
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>