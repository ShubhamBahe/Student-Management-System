@extends('layouts.dashboard')
@section('title', 'Students Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Students Report</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Complete student enrollment report</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.reports.export', ['students','csv']) }}"
                class="btn btn-success btn-sm">
                <i class="fas fa-file-csv me-1"></i>Export CSV
            </a>
        </div>
    </div>
    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #2563eb">
                <div class="fw-bold fs-3 text-primary">{{ $summary['total'] }}</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #10b981">
                <div class="fw-bold fs-3 text-success">{{ $summary['active'] }}</div>
                <div class="stat-label">Active</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #dc2626">
                <div class="fw-bold fs-3 text-danger">{{ $summary['inactive'] }}</div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #8b5cf6">
                <div class="fw-bold fs-3" style="color:#8b5cf6">{{ $summary['male'] }}</div>
                <div class="stat-label">Male</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #db2777">
                <div class="fw-bold fs-3" style="color:#db2777">{{ $summary['female'] }}</div>
                <div class="stat-label">Female</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left:4px solid #f59e0b">
                <div class="fw-bold fs-3 text-warning">{{ $students->count() > 0 ? round(($summary['active']/$summary['total'])*100) : 0 }}%</div>
                <div class="stat-label">Active Rate</div>
            </div>
        </div>
    </div>
    {{-- Filters --}}
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="gender" class="form-select form-select-sm">
                    <option value="">All Gender</option>
                    <option value="male"   {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('dashboard.reports.students') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Admission No</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $student->user->photo_url }}"
                                        class="rounded-circle" style="width:30px;height:30px;object-fit:cover">
                                    <span style="font-size:0.875rem;font-weight:500">{{ $student->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <small>{{ $student->admission_number }}</small>
                            </td>
                            <td>
                                <small>{{ $student->class->name ?? '-' }} / {{ $student->section->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ ucfirst($student->user->gender ?? '-') }}</small>
                            </td>
                            <td>
                                <small>{{ $student->user->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $student->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No students found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection