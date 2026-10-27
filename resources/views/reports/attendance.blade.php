@extends('layouts.dashboard')
@section('title', 'Attendance Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Attendance Report</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Monthly Attendance Report</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Student attendance for {{ \Carbon\Carbon::create(null, $month)->format('F') }} {{ $year }}</p>
        </div>
    </div>
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.8rem">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.8rem">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach([2024,2025,2026] as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.8rem">Class</label>
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-search me-1"></i>Generate Report
                </button>
            </div>
        </form>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th style="color:#10b981">Present</th>
                        <th style="color:#dc2626">Absent</th>
                        <th style="color:#f59e0b">Late</th>
                        <th>Total Days</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                        @php
                            $present  = $student->attendances->where('status','present')->count();
                            $absent   = $student->attendances->where('status','absent')->count();
                            $late     = $student->attendances->where('status','late')->count();
                            $total    = $student->attendances->count();
                            $pct      = $total > 0 ? round(($present / $total) * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div style="font-size:0.8rem;font-weight:500">{{ $student->user->name }}</div>
                                <small class="text-muted">{{ $student->roll_number }}</small>
                            </td>
                            <td>
                                <small>{{ $student->class->name ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-active">{{ $present }}</span>
                            </td>
                            <td>
                                <span class="badge badge-inactive">{{ $absent }}</span>
                            </td>
                            <td>
                                <span class="badge badge-pending">{{ $late }}</span>
                            </td>
                            <td>
                                <small>{{ $total }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="height:6px;width:60px;border-radius:3px">
                                        <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}"
                                            style="width:{{ $pct }}%"></div>
                                    </div>
                                    <small style="font-weight:600;color:{{ $pct >= 75 ? '#16a34a' : '#dc2626' }}">{{ $pct }}%</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection