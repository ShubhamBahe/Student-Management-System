<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — School Management System</title>
    <link rel="icon" type="image/png"
      href="https://ui-avatars.com/api/?name=SMS&background=2563eb&color=fff&size=64&bold=true&font-size=0.4">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            border: none;
        }
        .icon-box {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
        }
        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #94a3b8;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center mb-4">
            <div class="icon-box">
                <i class="fas fa-user-plus text-white fa-2x"></i>
            </div>
            <h4 class="fw-bold" style="color:#1e293b">Create Account</h4>
            <p class="text-muted" style="font-size:0.875rem">
                Register for School Management System
            </p>
        </div>
        @if(session('success'))
            <div class="alert alert-success py-2 mb-3">
                <i class="fas fa-check-circle me-2"></i>
                <small>{{ session('success') }}</small>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger py-2 mb-3">
                <small>{{ $errors->first() }}</small>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user" style="font-size:0.8rem"></i>
                        </span>
                        <input type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Muhammad Ali"
                            value="{{ old('name') }}" required>
                    </div>
                    @error('name')
                        <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">
                        Phone
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-phone" style="font-size:0.8rem"></i>
                        </span>
                        <input type="text" name="phone"
                            class="form-control"
                            placeholder="0300-0000000"
                            value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope" style="font-size:0.8rem"></i>
                        </span>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="your@email.com"
                            value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">
                        Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock" style="font-size:0.8rem"></i>
                        </span>
                        <input type="password" name="password" id="passField"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimum 8 characters" required>
                        <button type="button" class="input-group-text"
                                onclick="togglePass()"
                                style="border-left:none;border-radius:0 10px 10px 0;border:1.5px solid #e2e8f0">
                            <i class="fas fa-eye" id="eyeIcon" style="font-size:0.8rem;color:#94a3b8"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">
                        Confirm Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock" style="font-size:0.8rem"></i>
                        </span>
                        <input type="password" name="password_confirmation"
                            class="form-control"
                            placeholder="Repeat password" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:0.875rem;font-weight:500">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male"   {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>
            {{-- Password strength --}}
            <div class="mt-3 p-3 rounded" style="background:#f8fafc;font-size:0.8rem">
                <div class="fw-bold mb-2" style="color:#1e293b">Password requirements:</div>
                <div id="req-length" class="text-muted">
                    <i class="fas fa-circle me-1" style="font-size:8px"></i>At least 8 characters
                </div>
                <div id="req-match" class="text-muted mt-1">
                    <i class="fas fa-circle me-1" style="font-size:8px"></i>Passwords must match
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 text-white mt-4 mb-3">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
            <div class="text-center">
                <span style="font-size:0.875rem;color:#64748b">Already have an account?</span>
                <a href="{{ route('login') }}"
                    style="font-size:0.875rem;color:#2563eb;text-decoration:none;margin-left:4px">
                    Sign In
                </a>
            </div>
        </form>
        {{-- Info Box --}}
        <div class="mt-4 p-3 rounded" style="background:#f0f9ff;border:1px solid #bfdbfe;font-size:0.8rem">
            <i class="fas fa-info-circle text-primary me-2"></i>
            <strong>Note:</strong> After registration your account needs to be approved by the Admin before you can login.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass() {
        const field = document.getElementById('passField');
            const icon  = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        document.getElementById('passField').addEventListener('input', function() {
            const val = this.value;
            document.getElementById('req-length').style.color = val.length >= 8 ? '#16a34a' : '#94a3b8';
        });
    </script>
</body>
</html>