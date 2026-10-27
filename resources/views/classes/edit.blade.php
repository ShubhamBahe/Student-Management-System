@extends('layouts.dashboard')
@section('title', 'Edit Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Class</h6>
                <form method="POST" action="{{ route('dashboard.classes.update', $class) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $class->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numeric Name</label>
                        <input type="text" name="numeric_name" class="form-control"
                            value="{{ old('numeric_name', $class->numeric_name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}"
                                    {{ $class->academic_year_id == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                        <a href="{{ route('dashboard.classes.index') }}"
                            class="btn btn-secondary">Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">
                    All Classes ({{ count($classes) }})
                </h6>
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
                            @forelse($classes as $i => $c)
                                <tr class="{{ $c->id == $class->id ? 'table-primary' : '' }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $c->name }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $c->academicYear->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#dbeafe;color:#1d4ed8">
                                            {{ $c->sections->count() }} sections
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-active">
                                            {{ $c->students->count() }} students
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('dashboard.classes.edit', $c) }}"
                                                class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="dc-{{ $c->id }}" method="POST"
                                                action="{{ route('dashboard.classes.destroy', $c) }}">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="ajaxDelete('dc-{{ $c->id }}', '{{ $c->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No classes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection