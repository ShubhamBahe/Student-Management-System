@extends('layouts.dashboard')
@section('title', 'Sections')

@section('breadcrumb')
    <li class="breadcrumb-item active">Sections</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">Add New Section</h6>
                <form method="POST" action="{{ route('dashboard.sections.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control" value="40">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-2"></i>Add Section</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">All Sections ({{ $sections->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Section</th>
                                <th>Class</th>
                                <th>Capacity</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sections as $i => $section)
                                <tr id="row-s{{ $section->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $section->name }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $section->class->name ?? '-' }}</small>
                                    </td>
                                    <td>{{ $section->capacity }}</td>
                                    <td>
                                        <span class="badge badge-active">{{ $section->students->count() }}</span>
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                id="del-btn-s{{ $section->id }}"
                                                onclick="ajaxDelete('{{ route('dashboard.sections.destroy', $section) }}', 's{{ $section->id }}', '{{ $section->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No sections found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection