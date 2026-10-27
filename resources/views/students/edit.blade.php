@extends('layouts.dashboard')
@section('title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Student — {{ $student->user->name }}</h6>
        <form method="POST" action="{{ route('dashboard.students.update', $student) }}"
            enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">
                        Personal Information
                    </h6><hr>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $student->user->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $student->user->email) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $student->user->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control"
                        value="{{ old('date_of_birth', $student->user->date_of_birth) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male"   {{ $student->user->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $student->user->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">Select</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" {{ $student->user->blood_group == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control"
                        value="{{ old('address', $student->user->address) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Update Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*"
                        onchange="previewPhoto(this)">
                    <img id="photoPreview" src="{{ $student->user->photo_url }}"
                        class="mt-2 rounded-circle"
                        style="width:60px;height:60px;object-fit:cover;">
                </div>

                <div class="col-12 mt-2">
                    <h6 class="text-muted" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px">
                        Academic Information
                    </h6><hr>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" required>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ $student->class_id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section_id" class="form-select" required>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}"
                                {{ $student->section_id == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ $student->academic_year_id == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"      {{ $student->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive"    {{ $student->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="transferred" {{ $student->status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="graduated"   {{ $student->status == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previous School</label>
                    <input type="text" name="previous_school" class="form-control"
                        value="{{ old('previous_school', $student->previous_school) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medical Conditions</label>
                    <input type="text" name="medical_conditions" class="form-control"
                        value="{{ old('medical_conditions', $student->medical_conditions) }}"
                        placeholder="None">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Student
                    </button>
                    <a href="{{ route('dashboard.students.show', $student) }}" class="btn btn-secondary">Cancel</a>
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
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush