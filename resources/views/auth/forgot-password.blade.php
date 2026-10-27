<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — School Management System</title>
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
            max-width: 420px;
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
                <i class="fas fa-key text-white fa-2x"></i>
            </div>
            <h4 class="fw-bold" style="color:#1e293b">Forgot Password?</h4>
            <p class="text-muted" style="font-size:0.875rem">
                Enter your email and we will send you a password reset link.
            </p>
        </div>
        @if(session('success'))
            <div class="alert alert-success py-2 mb-3">
                <i class="fas fa-check-circle me-2"></i>
                <small>{{ session('success') }}</small>
            </div>
        @endif
        @if(session('status'))
            <div class="alert alert-success py-2 mb-3">
                <i class="fas fa-check-circle me-2"></i>
                <small>{{ session('status') }}</small>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger py-2 mb-3">
                <small>{{ $errors->first() }}</small>
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label" style="font-size:0.875rem;font-weight:500">
                    Email Address
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope" style="font-size:0.8rem"></i>
                    </span>
                    <input type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="your@email.com"
                        value="{{ old('email') }}"
                        required autofocus>
                </div>
                @error('email')
                    <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 text-white mb-3">
                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
            </button>
            <div class="text-center">
                <a href="{{ route('login') }}"
                style="font-size:0.875rem;color:#2563eb;text-decoration:none">
                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                </a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>