<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission — {{ setting('school_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('public.partials.style')
</head>
<body>
    {{-- Navbar section --}}
    @include('public.partials.navbar')
    <div style="background:linear-gradient(135deg,#1e293b,#2563eb);padding:80px 0;text-align:center;color:#fff">
        <h1 class="fw-bold mb-2">Online Admission</h1>
        <p class="opacity-80">Apply for admission — Session 2025-2026</p>
    </div>
    <section style="padding:80px 0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    <div class="page-card">
                        <h5 class="fw-bold mb-4" style="color:#1e293b"><i class="fas fa-user-plus me-2 text-primary"></i>Admission Application Form</h5>
                        <form method="POST" action="{{ route('admission.apply') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Student Information</h6><hr></div>
                                <div class="col-md-6">
                                    <label class="form-label">Student Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="student_name" class="form-control" placeholder="Muhammad Ali Khan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Applying for Class <span class="text-danger">*</span></label>
                                    <select name="class" class="form-select" required>
                                        <option value="">Select Class</option>
                                        @for($i=1;$i<=10;$i++)
                                            <option>Class {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Previous School</label>
                                    <input type="text" name="previous_school" class="form-control" placeholder="Previous school name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Class Passed</label>
                                    <input type="text" name="last_class" class="form-control" placeholder="e.g. Class 4">
                                </div>
                                <div class="col-12 mt-2"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Parent/Guardian Information</h6><hr></div>
                                <div class="col-md-6">
                                    <label class="form-label">Parent/Guardian Name <span class="text-danger">*</span></label>
                                    <input type="text" name="parent_name" class="form-control" placeholder="Father/Guardian name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="0300-0000000" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="parent@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CNIC</label>
                                    <input type="text" name="cnic" class="form-control" placeholder="42301-0000000-0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Home Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Complete home address"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-5 py-3 w-100" style="border-radius:10px;font-weight:600;font-size:1rem">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Admission Application
                                    </button>
                                    <small class="text-muted d-block text-center mt-2">We will contact you within 24 hours after reviewing your application.</small>
                                </div>
                            </div>
                        </form>
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