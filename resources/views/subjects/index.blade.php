@extends('layouts.dashboard')
@section('title', 'Subjects')

@section('breadcrumb')
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">Add New Subject</h6>
                <form method="POST" action="{{ route('dashboard.subjects.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mathematics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. MATH" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Marks</label>
                        <input type="number" name="full_marks" class="form-control" value="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pass Marks</label>
                        <input type="number" name="pass_marks" class="form-control" value="33">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-2"></i>Add Subject</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">All Subjects ({{ $subjects->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Code</th>
                                <th>Full Marks</th>
                                <th>Pass Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects as $i => $subject)
                                <tr id="row-sub{{ $subject->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <strong>{{ $subject->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $subject->code }}</span>
                                    </td>
                                    <td>{{ $subject->full_marks }}</td>
                                    <td>{{ $subject->pass_marks }}</td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-sub{{ $subject->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.subjects.destroy', $subject) }}', 'sub{{ $subject->id }}', '{{ $subject->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No subjects found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection