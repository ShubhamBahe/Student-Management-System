@extends('layouts.dashboard')
@section('title', 'Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Attendance</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Daily student attendance records</p>
        </div>
        @can('mark attendance')
            <a href="{{ route('dashboard.attendance.mark') }}" class="btn btn-success">
                <i class="fas fa-clipboard-check me-2"></i>Mark Attendance
            </a>
        @endcan
    </div>
    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #2563eb">
                <div class="fw-bold fs-3 text-primary">{{ $summary['present'] }}</div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #dc2626">
                <div class="fw-bold fs-3 text-danger">{{ $summary['absent'] }}</div>
                <div class="stat-label">Absent Today</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #f59e0b">
                <div class="fw-bold fs-3 text-warning">{{ $summary['late'] }}</div>
                <div class="stat-label">Late Today</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #10b981">
                <div class="fw-bold fs-3 text-success">{{ $summary['half_day'] }}</div>
                <div class="stat-label">Half Day</div>
            </div>
        </div>
    </div>
    {{-- Filter --}}
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label" style="font-size:0.8rem">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:0.8rem">Class</label>
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('dashboard.attendance.report') }}" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-chart-bar me-1"></i>Monthly Report
                </a>
            </div>
        </form>
    </div>
    <div class="page-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color:#1e293b">Attendance for {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h6>
            <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $attendances->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $att->student->user->photo_url }}"
                                        class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                                    <small style="font-weight:500">{{ $att->student->user->name }}</small>
                                </div>
                            </td>
                            <td>
                                <small>{{ $att->class->name ?? '-' }} / {{ $att->section->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                @if($att->status === 'present')
                                    <span class="badge badge-active">Present</span>
                                @elseif($att->status === 'absent')
                                    <span class="badge badge-inactive">Absent</span>
                                @elseif($att->status === 'late')
                                    <span class="badge badge-pending">Late</span>
                                @else
                                    <span class="badge bg-secondary">Half Day</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $att->remarks ?? '-' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard fa-2x mb-2 d-block"></i>
                                No attendance marked for this date
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $attendances->withQueryString()->links() }}
    </div>
@endsection