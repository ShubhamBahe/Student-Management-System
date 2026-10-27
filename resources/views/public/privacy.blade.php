<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Privacy Policy</h1>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-card">
                        <h5 class="fw-bold mb-3">Privacy Policy</h5>
                        <p class="text-muted">Last updated: {{ date('d M Y') }}</p>
                        <p class="text-muted">{{ setting('school_name') }} is committed to protecting the privacy of students, parents, and staff. This policy explains how we collect, use, and protect your information.</p>
                        <h6 class="fw-bold mt-4">Information We Collect</h6>
                        <p class="text-muted">We collect personal information including names, contact details, academic records, and payment information as necessary for school operations.</p>
                        <h6 class="fw-bold mt-4">How We Use Information</h6>
                        <p class="text-muted">Information is used solely for educational and administrative purposes including student management, fee collection, and communication with parents.</p>
                        <h6 class="fw-bold mt-4">Data Security</h6>
                        <p class="text-muted">We implement appropriate security measures to protect personal information from unauthorized access or disclosure.</p>
                        <h6 class="fw-bold mt-4">Contact</h6>
                        <p class="text-muted">For privacy concerns, contact us at {{ setting('school_email') }}</p>
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