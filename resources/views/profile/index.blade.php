@extends('layouts.dashboard')
@section('title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="page-card text-center">
                <img src="{{ auth()->user()->photo_url }}"
                    class="rounded-circle mb-3"
                    style="width:100px;height:100px;object-fit:cover;border:4px solid #dbeafe;">
                <h5 class="fw-bold mb-1" style="color:#1e293b">{{ auth()->user()->name }}</h5>
                <p class="text-primary mb-2" style="font-size:0.875rem">
                    {{ str_replace('_', ' ', ucfirst(auth()->user()->getRoleNames()->first() ?? 'User')) }}
                </p>
                <span class="badge badge-active mb-3">Active</span>
                <hr>
                <div class="text-start">
                    <div class="mb-2 d-flex gap-2">
                        <i class="fas fa-envelope text-primary mt-1" style="width:16px"></i>
                        <small>{{ auth()->user()->email }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <i class="fas fa-phone text-primary mt-1" style="width:16px"></i>
                        <small>{{ auth()->user()->phone ?? 'Not set' }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <i class="fas fa-venus-mars text-primary mt-1" style="width:16px"></i>
                        <small>{{ ucfirst(auth()->user()->gender ?? 'Not set') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <i class="fas fa-calendar text-primary mt-1" style="width:16px"></i>
                        <small>Joined: {{ auth()->user()->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <i class="fas fa-map-marker-alt text-primary mt-1" style="width:16px"></i>
                        <small>{{ auth()->user()->address ?? 'Not set' }}</small>
                    </div>
                </div>
            </div>
        </div>
        {{-- Edit Profile --}}
        <div class="col-lg-8">
            <div class="page-card mb-4">
                <h6 class="fw-bold mb-4" style="color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:12px">
                    <i class="fas fa-user-edit text-primary me-2"></i>Edit Profile
                </h6>
                @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', auth()->user()->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male"   {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ auth()->user()->blood_group == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', auth()->user()->address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*"
                                onchange="previewPhoto(this)">
                            <img id="photoPreview" src="{{ auth()->user()->photo_url }}"
                                class="mt-2 rounded-circle"
                                style="width:60px;height:60px;object-fit:cover;">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('photoPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush