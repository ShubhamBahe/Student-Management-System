@extends('layouts.dashboard')
@section('title', 'Add Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Add Student</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Register New Student</h6>
        <form method="POST" action="{{ route('dashboard.students.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                {{-- Personal Info --}}
                <div class="col-12"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Personal Information</h6><hr></div>
                <div class="col-md-4">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="male"   {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">Select</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*"
                        onchange="previewPhoto(this)">
                    <img id="photoPreview" src="#" alt="Preview"
                        class="mt-2 rounded" style="width:80px;height:80px;object-fit:cover;display:none;">
                </div>
                {{-- Academic Info --}}
                <div class="col-12 mt-2"><h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">Academic Information</h6><hr></div>
                <div class="col-md-4">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" required id="classSelect"g
                            onchange="loadSections(this.value, 'sectionSelect')">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-select" required id="sectionSelect">
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select" required>
                        <option value="">Select Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                    <input type="date" name="admission_date" class="form-control"
                        value="{{ old('admission_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previous School</label>
                    <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medical Conditions</label>
                    <input type="text" name="medical_conditions" class="form-control" value="{{ old('medical_conditions') }}" placeholder="None">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Register Student
                    </button>
                    <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary">Cancel</a>
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