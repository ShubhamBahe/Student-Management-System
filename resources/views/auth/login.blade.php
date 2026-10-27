<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — School Management System</title>
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
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .login-logo {
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
        .btn-login {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }
        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #94a3b8;
        }
        .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo">
                <i class="fas fa-graduation-cap text-white fa-2x"></i>
            </div>
            <h4 class="fw-bold" style="color:#1e293b">School Management System</h4>
            <p class="text-muted" style="font-size:0.875rem">Sign in to your account</p>
        </div>
        @if(session('success'))
            <div class="alert alert-success py-2"><small>{{ session('success') }}</small></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger py-2"><small>{{ $errors->first() }}</small></div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-500" style="font-size:0.875rem;font-weight:500;">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope" style="font-size:0.8rem"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="admin@school.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:0.875rem;font-weight:500;">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock" style="font-size:0.8rem"></i></span>
                    <input type="password" name="password" id="passwordField"
                        class="form-control" placeholder="••••••••" required>
                    <button type="button" class="input-group-text" onclick="togglePassword()"
                            style="cursor:pointer;background:#f8fafc;border-left:none">
                        <i class="fas fa-eye" id="eyeIcon" style="font-size:0.8rem;color:#94a3b8"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:0.875rem">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" style="font-size:0.875rem;color:#2563eb">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
            <div class="text-center mt-3">
                <span style="font-size:0.875rem;color:#64748b">Don't have an account?</span>
                <a href="{{ route('register') }}"
                    style="font-size:0.875rem;color:#2563eb;text-decoration:none;margin-left:4px;font-weight:500">
                    Register here
                </a>
            </div>
        </form>
        <div class="mt-4 p-3 rounded" style="background:#f8fafc;font-size:0.8rem">
            <strong>Default Logins:</strong><br>
            Super Admin: superadmin@school.com<br>
            Admin: admin@school.com<br>
            Password: password123
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const field = document.getElementById('passwordField');
            const icon  = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>