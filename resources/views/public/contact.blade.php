<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Contact Us</h1>
        <nav aria-label="breadcrumb" class="justify-content-center d-flex">
            <ol class="breadcrumb" style="background:none">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.7)">Home</a>
                </li>
                <li class="breadcrumb-item active text-white">Contact</li>
            </ol>
        </nav>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h4 class="fw-bold mb-4" style="color:#1e293b">Get In Touch</h4>
                    <div class="d-flex gap-3 mb-4">
                        <div style="width:50px;height:50px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Address</div>
                            <small class="text-muted">{{ setting('school_address') }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <div style="width:50px;height:50px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Phone</div>
                            <small class="text-muted">{{ setting('school_phone') }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <div style="width:50px;height:50px;background:#fef9c3;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#ca8a04;flex-shrink:0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">Email</div>
                            <small class="text-muted">{{ setting('school_email') }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <div style="width:50px;height:50px;background:#fce7f3;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#db2777;flex-shrink:0">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem">School Hours</div>
                            <small class="text-muted">Mon–Sat: 7:30 AM – 2:00 PM</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="page-card">
                        <h5 class="fw-bold mb-4" style="color:#1e293b">Send Us a Message</h5>
                        @if(session('success'))
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('contact.send') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Muhammad Ali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="ali@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="0300-0000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject</label>
                                    <select name="subject" class="form-select">
                                        <option>General Inquiry</option>
                                        <option>Admission Query</option>
                                        <option>Fee Information</option>
                                        <option>Complaint</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-5 py-3" style="border-radius:10px;font-weight:600">
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
    @include('public.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>