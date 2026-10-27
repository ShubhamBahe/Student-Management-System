<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">News & Blog</h1>
        <nav aria-label="breadcrumb" class="justify-content-center d-flex">
            <ol class="breadcrumb" style="background:none">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.7)">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">News</li>
            </ol>
        </nav>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row g-4">
                @forelse($news as $item)
                    <div class="col-md-4">
                        <div class="news-card h-100">
                            <div style="height:200px;overflow:hidden;border-radius:16px 16px 0 0;position:relative">
                                <img src="https://picsum.photos/seed/news{{ $loop->index + 1 }}/600/400"
                                    alt="{{ $item->title }}"
                                    style="width:100%;height:200px;object-fit:cover;transition:transform 0.3s"
                                    onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'"
                                    loading="lazy">
                                <div style="position:absolute;top:12px;left:12px">
                                    <span style="background:#2563eb;color:#fff;padding:4px 10px;
                                                border-radius:20px;font-size:0.75rem;font-weight:600">
                                        News
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                                </small>
                                <h6 class="fw-bold mt-2 mb-2" style="color:#1e293b">{{ $item->title }}</h6>
                                <p class="text-muted mb-3" style="font-size:0.875rem">
                                    {{ Str::limit($item->content, 120) }}
                                </p>
                                <a href="{{ route('news.detail', $item->slug) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    Read More <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-newspaper fa-3x mb-3 d-block"></i>No news found
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $news->links() }}</div>
        </div>
    </section>
    {{-- Footer section --}}
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>