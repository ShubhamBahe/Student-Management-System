<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Structure — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Fee Structure</h1>
        <p class="opacity-80">Session 2025-2026</p>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="page-card">
                        <h5 class="fw-bold mb-4" style="color:#1e293b">Annual Fee Structure 2025-2026</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background:#f8fafc">
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount (PKR)</th>
                                        <th>Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($feeTypes as $fee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width:8px;height:8px;background:#2563eb;border-radius:50%"></div>
                                                    <strong>{{ $fee->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-primary fw-bold">PKR {{ number_format($fee->amount) }}</td>
                                            <td>
                                                <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ ucfirst($fee->frequency) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No fee structure available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
                            <small class="text-success">
                                <i class="fas fa-info-circle me-2"></i>Note: Fees are subject to change. Contact school office for latest information.
                            </small>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('admission') }}" class="btn btn-primary px-5 py-3" style="border-radius:10px;font-weight:600">
                            <i class="fas fa-user-plus me-2"></i>Apply for Admission
                        </a>
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