@extends('layouts.dashboard')
@section('title', 'Classes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Classes</li>
@endsection

@section('content')
    <div class="row g-4">
        {{-- Add Class Form --}}
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">Add New Class</h6>
                <form method="POST" action="{{ route('dashboard.classes.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="e.g. Class 1" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numeric Name</label>
                        <input type="text" name="numeric_name" class="form-control" value="{{ old('numeric_name') }}" placeholder="e.g. 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Class
                    </button>
                </form>
            </div>
        </div>
        {{-- Classes List --}}
        <div class="col-md-8">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">All Classes ({{ $classes->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Class</th>
                                <th>Academic Year</th>
                                <th>Sections</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $i => $class)
                                <tr id="row-c{{ $class->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $class->name }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $class->academicYear->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $class->sections->count() }} sections</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-active">{{ $class->students->count() }} students</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('dashboard.classes.edit', $class) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-danger"
                                                id="del-btn-c{{ $class->id }}"
                                                onclick="ajaxDelete('{{ route('dashboard.classes.destroy', $class) }}', 'c{{ $class->id }}', '{{ $class->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No classes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection