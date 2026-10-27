@extends('layouts.dashboard')
@section('title', 'Settings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">System Settings</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage school information and system configuration</p>
        </div>
    </div>
    <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- School Information --}}
            <div class="col-lg-8">
                <div class="page-card mb-4">
                    <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                        <i class="fas fa-school text-primary me-2"></i>School Information
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">School Name <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control"
                                value="{{ $settings['school_name'] ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="school_tagline" class="form-control"
                                value="{{ $settings['school_tagline'] ?? '' }}"
                                placeholder="Education is the key to success">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="school_email" class="form-control"
                                value="{{ $settings['school_email'] ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="school_phone" class="form-control"
                                value="{{ $settings['school_phone'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="school_address" class="form-control"
                                value="{{ $settings['school_address'] ?? '' }}">
                        </div>
                    </div>
                </div>
                {{-- System Configuration --}}
                <div class="page-card mb-4">
                    <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                        <i class="fas fa-cog text-primary me-2"></i>System Configuration
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select">
                                @foreach(['PKR','USD','GBP','EUR','SAR','AED'] as $cur)
                                    <option value="{{ $cur }}" {{ ($settings['currency'] ?? 'PKR') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select">
                                @foreach(['Asia/Karachi','Asia/Dubai','Asia/Riyadh','Europe/London','America/New_York'] as $tz)
                                    <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'Asia/Karachi') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Academic Session</label>
                            <input type="text" name="session_year" class="form-control"
                                value="{{ $settings['session_year'] ?? '2025-2026' }}"
                                placeholder="2025-2026">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Library Fine Per Day (PKR)</label>
                            <input type="number" name="fine_per_day" class="form-control"
                                value="{{ $settings['fine_per_day'] ?? '5' }}" min="0">
                        </div>
                    </div>
                </div>
                {{-- Email Settings --}}
                <div class="page-card">
                    <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                        <i class="fas fa-envelope text-primary me-2"></i>Email Settings
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="mail_host" class="form-control"
                                value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SMTP Port</label>
                            <input type="text" name="mail_port" class="form-control"
                                value="{{ $settings['mail_port'] ?? '587' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Username</label>
                            <input type="text" name="mail_username" class="form-control"
                                value="{{ $settings['mail_username'] ?? '' }}"
                                placeholder="your@gmail.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Password</label>
                            <input type="password" name="mail_password" class="form-control"
                                placeholder="Leave blank to keep current">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Right Sidebar --}}
            <div class="col-lg-4">
                {{-- Logo Upload --}}
                <div class="page-card mb-4">
                    <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                        <i class="fas fa-image text-primary me-2"></i>School Logo
                    </h6>
                    <div class="text-center mb-3">
                        @php 
                            $logoVal = $settings['school_logo'] ?? null; 
                        @endphp
                        @if($logoVal && file_exists(storage_path('app/public/logos/' . $logoVal)))
                            <img src="{{ asset('storage/logos/' . $logoVal) }}"
                                class="rounded mb-2"
                                style="width:80px;height:80px;object-fit:cover;border:2px solid #e2e8f0;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center
                                        fw-bold text-white mx-auto mb-2"
                                style="width:80px;height:80px;background:#2563eb;font-size:2rem">
                                {{ strtoupper(substr(setting('school_name', 'S'), 0, 1)) }}
                            </div>
                        @endif
                        <small class="text-muted d-block">Current Logo</small>
                    </div>
                    <label class="form-label">Upload New Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*"
                        onchange="previewLogo(this)">
                    <img id="logoPreview" src="#" alt="Preview"
                        class="mt-2 rounded" style="width:80px;height:80px;object-fit:cover;display:none;">
                    <small class="text-muted d-block mt-1">Recommended: 200x200px, PNG or JPG</small>
                </div>
                {{-- Current Settings Summary --}}
                <div class="page-card mb-4">
                    <h6 class="fw-bold mb-3" style="color:#1e293b">Current Settings</h6>
                    <div class="mb-2 d-flex justify-content-between">
                        <small class="text-muted">Currency</small>
                        <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $settings['currency'] ?? 'PKR' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <small class="text-muted">Timezone</small>
                        <span class="badge" style="background:#dcfce7;color:#166534">{{ $settings['timezone'] ?? 'Asia/Karachi' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <small class="text-muted">Session</small>
                        <span class="badge" style="background:#fef9c3;color:#854d0e">{{ $settings['session_year'] ?? '2025-2026' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <small class="text-muted">Fine/Day</small>
                        <span class="badge" style="background:#fee2e2;color:#991b1b">PKR {{ $settings['fine_per_day'] ?? '5' }}</span>
                    </div>
                </div>
                {{-- Save Button --}}
                <div class="page-card">
                    <button type="submit" class="btn btn-primary w-100 py-3" style="font-weight:600;font-size:1rem">
                        <i class="fas fa-save me-2"></i>Save All Settings
                    </button>
                    <div class="mt-3 p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <small class="text-success">
                            <i class="fas fa-info-circle me-1"></i>
                            Settings are saved to database and applied instantly.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('logoPreview').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush