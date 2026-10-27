@extends('layouts.dashboard')
@section('title', 'Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Students</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage all registered students</p>
        </div>
        @can('create students')
            <a href="{{ route('dashboard.students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Student
            </a>
        @endcan
    </div>
    {{-- Filters --}}
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text"
                    name="search"
                    id="liveSearch"
                    class="form-control form-control-sm"
                    placeholder="Search name, email, roll no..."
                    value="{{ request('search') }}"
                    oninput="liveStudentSearch(this.value)"
                    autocomplete="off">
            </div>
            <div class="col-md-2">
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="section_id" class="form-select form-select-sm">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active"      {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive"    {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
    {{-- Table --}}
    <div class="page-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- Total count --}}
            <span style="font-size:0.875rem;color:#64748b">
                Total: <strong id="totalCount">{{ $students->total() }}</strong> students
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Admission No</th>
                        <th>Roll No</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    @forelse($students as $i => $student)
                        <tr id="student-row-{{ $student->id }}">
                            <td>{{ $students->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $student->user->photo_url }}"
                                        class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                    <div>
                                        <div style="font-weight:500;font-size:0.875rem">{{ $student->user->name }}</div>
                                        <small class="text-muted">{{ $student->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ $student->admission_number }}</small>
                            </td>
                            <td>
                                <small>{{ $student->roll_number }}</small>
                            </td>
                            <td>
                                <small>{{ $student->class->name ?? '-' }} / {{ $student->section->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ ucfirst($student->user->gender ?? '-') }}</small>
                            </td>
                            <td>
                                @if($student->status === 'active')
                                    <span class="badge badge-active">Active</span>
                                @elseif($student->status === 'inactive')
                                    <span class="badge badge-inactive">Inactive</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.students.show', $student) }}"
                                        class="btn btn-sm btn-info text-white" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit students')
                                        <a href="{{ route('dashboard.students.edit', $student) }}"
                                            class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    <a href="{{ route('dashboard.students.id-card', $student) }}"
                                        class="btn btn-sm btn-secondary" title="ID Card">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    @can('delete students')
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="ajaxDeleteStudent({{ $student->id }}, '{{ $student->user->name }}')"
                                                title="Delete" id="del-btn-{{ $student->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                                No students found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $students->withQueryString()->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        function ajaxDeleteStudent(id, name) {
            Swal.fire({
                title: 'Delete Student?',
                text: 'Are you sure you want to delete "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    document.getElementById('del-btn-' + id).innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    document.getElementById('del-btn-' + id).disabled = true;
                    $.ajax({
                        url: '/dashboard/students/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Remove the row from table
                            $('#student-row-' + id).fadeOut(400, function() {
                                $(this).remove();
                                // Update total count
                                let current = parseInt($('#totalCount').text());
                                $('#totalCount').text(current - 1);
                            });
                            // Show success toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: name + ' deleted successfully!',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete student. Please try again.', 'error');
                            document.getElementById('del-btn-' + id).innerHTML = '<i class="fas fa-trash"></i>';
                            document.getElementById('del-btn-' + id).disabled = false;
                        }
                    });
                }
            });
        }
    </script>
@endpush