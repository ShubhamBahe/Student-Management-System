@extends('layouts.dashboard')
@section('title', 'Edit Teacher')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Teacher — {{ $teacher->user->name }}</h6>
        <form method="POST" action="{{ route('dashboard.teachers.update', $teacher) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->user->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->user->email) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->user->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male"   {{ $teacher->user->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $teacher->user->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $teacher->qualification) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $teacher->specialization) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salary (PKR)</label>
                    <input type="number" name="salary" class="form-control" value="{{ old('salary', $teacher->salary) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ $teacher->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $teacher->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $teacher->user->address) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Update Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                    <img id="photoPreview" src="{{ $teacher->user->photo_url }}"
                        class="mt-2 rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Teacher
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
                reader.onload = e => { document.getElementById('photoPreview').src = e.target.result; };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush