@extends('layouts.dashboard')
@section('title', 'Change Password')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.profile') }}">Profile</a></li>
    <li class="breadcrumb-item active">Change Password</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                    <i class="fas fa-key text-primary me-2"></i>Change Password
                </h6>
                @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('dashboard.password.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="currentPass"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Enter current password" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass('currentPass')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="newPass"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimum 8 characters" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass('newPass')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1" style="font-size:0.8rem">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmPass"
                                    class="form-control"
                                    placeholder="Repeat new password" required>
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePass('confirmPass')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Password strength --}}
                        <div class="col-12">
                            <div style="background:#f8fafc;border-radius:8px;padding:12px;font-size:0.8rem">
                                <div class="fw-bold mb-2" style="color:#1e293b">Password Requirements:</div>
                                <div id="req-length"  class="text-muted"><i class="fas fa-circle me-2" style="font-size:8px"></i>At least 8 characters</div>
                                <div id="req-upper"   class="text-muted mt-1"><i class="fas fa-circle me-2" style="font-size:8px"></i>At least one uppercase letter</div>
                                <div id="req-number"  class="text-muted mt-1"><i class="fas fa-circle me-2" style="font-size:8px"></i>At least one number</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                            <a href="{{ route('dashboard.profile') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        document.getElementById('newPass').addEventListener('input', function() {
            const val = this.value;
            const reqLength = document.getElementById('req-length');
            const reqUpper  = document.getElementById('req-upper');
            const reqNumber = document.getElementById('req-number');

            reqLength.style.color = val.length >= 8 ? '#16a34a' : '#94a3b8';
            reqUpper.style.color  = /[A-Z]/.test(val) ? '#16a34a' : '#94a3b8';
            reqNumber.style.color = /[0-9]/.test(val) ? '#16a34a' : '#94a3b8';
        });
    </script>
@endpush