@extends('layouts.dashboard')
@section('title', 'Add Teacher')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">Add Teacher</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Add New Teacher</h6>
        <form method="POST" action="{{ route('dashboard.teachers.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-12"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Personal Information</h6><hr></div>
                <div class="col-md-4">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="male"   {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                    <img id="photoPreview" src="#" class="mt-2 rounded" style="width:80px;height:80px;object-fit:cover;display:none;">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-12 mt-2"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Professional Information</h6><hr></div>
                <div class="col-md-4">
                    <label class="form-label">Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification') }}" placeholder="e.g. M.Sc Mathematics" required>
                    @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization') }}" placeholder="e.g. Mathematics">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monthly Salary (PKR) <span class="text-danger">*</span></label>
                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" min="0" required>
                    @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Add Teacher
                    </button>
                    <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('photoPreview').src = e.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush