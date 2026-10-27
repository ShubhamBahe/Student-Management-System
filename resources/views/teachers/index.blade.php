@extends('layouts.dashboard')
@section('title', 'Teachers')

@section('breadcrumb')
    <li class="breadcrumb-item active">Teachers</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Teachers</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage all teaching staff</p>
        </div>
        @can('create teachers')
            <a href="{{ route('dashboard.teachers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Teacher
            </a>
        @endcan
    </div>
    {{-- Filters --}}
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search name, email, employee ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
    <div class="page-card">
        {{-- Total count --}}
        <div class="mb-3">
            <span style="font-size:0.875rem;color:#64748b">Total: <strong id="teacherCount">{{ $teachers->total() }}</strong> teachers</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Employee ID</th>
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $i => $teacher)
                        <tr id="row-t{{ $teacher->id }}">
                            <td>{{ $teachers->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $teacher->user->photo_url }}"
                                        class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                                    <div>
                                        <div style="font-weight:500;font-size:0.875rem">{{ $teacher->user->name }}</div>
                                        <small class="text-muted">{{ $teacher->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="fw-500">{{ $teacher->employee_id }}</small>
                            </td>
                            <td>
                                <small>{{ $teacher->specialization ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $teacher->qualification }}</small>
                            </td>
                            <td>
                                <small>PKR {{ number_format($teacher->salary) }}</small>
                            </td>
                            <td>
                                @if($teacher->status === 'active')
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.teachers.show', $teacher) }}"
                                        class="btn btn-sm btn-info text-white" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit teachers')
                                        <a href="{{ route('dashboard.teachers.edit', $teacher) }}"
                                            class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete teachers')
                                        <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-t{{ $teacher->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.teachers.destroy', $teacher) }}', 't{{ $teacher->id }}', '{{ $teacher->user->name }}', 'teacherCount')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>No teachers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $teachers->withQueryString()->links() }}
    </div>
@endsection