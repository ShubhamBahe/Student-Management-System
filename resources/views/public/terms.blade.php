<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Footer section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Terms & Conditions</h1>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-card">
                        <h5 class="fw-bold mb-3">Terms & Conditions</h5>
                        <p class="text-muted">By enrolling your child at {{ setting('school_name') }}, you agree to the following terms and conditions.</p>
                        <h6 class="fw-bold mt-4">Fee Payment</h6>
                        <p class="text-muted">Fees must be paid by the 10th of each month. Late payment will incur a fine as per school policy.</p>
                        <h6 class="fw-bold mt-4">Attendance</h6>
                        <p class="text-muted">Students must maintain a minimum of 75% attendance. Consistent absence may result in removal from the roll.</p>
                        <h6 class="fw-bold mt-4">Conduct</h6>
                        <p class="text-muted">Students are expected to follow school rules and maintain good conduct at all times.</p>
                        <h6 class="fw-bold mt-4">Uniform</h6>
                        <p class="text-muted">Students must wear the prescribed school uniform every day.</p>
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