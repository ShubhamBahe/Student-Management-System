@extends('layouts.dashboard')
@section('title', 'Student Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-xl-4">
            <div class="page-card text-center">
                <img src="{{ $student->user->photo_url }}"
                    class="rounded-circle mb-3"
                    style="width:100px;height:100px;object-fit:cover;border:4px solid #dbeafe;">
                <h5 class="fw-bold mb-1" style="color:#1e293b">{{ $student->user->name }}</h5>
                <p class="text-muted mb-2" style="font-size:0.875rem">{{ $student->class->name ?? '-' }} — {{ $student->section->name ?? '-' }}</p>
                <span class="badge {{ $student->status === 'active' ? 'badge-active' : 'badge-inactive' }} mb-3">
                    {{ ucfirst($student->status) }}
                </span>
                <hr>
                <div class="text-start">
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-id-card text-primary" style="width:18px"></i>
                        <small><strong>Admission No:</strong> {{ $student->admission_number }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-list-ol text-primary" style="width:18px"></i>
                        <small><strong>Roll No:</strong> {{ $student->roll_number }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-envelope text-primary" style="width:18px"></i>
                        <small>{{ $student->user->email }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-phone text-primary" style="width:18px"></i>
                        <small>{{ $student->user->phone ?? 'N/A' }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-venus-mars text-primary" style="width:18px"></i>
                        <small>{{ ucfirst($student->user->gender ?? 'N/A') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-birthday-cake text-primary" style="width:18px"></i>
                        <small>{{ $student->user->date_of_birth ? \Carbon\Carbon::parse($student->user->date_of_birth)->format('d M Y') : 'N/A' }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-calendar text-primary" style="width:18px"></i>
                        <small>Admitted: {{ \Carbon\Carbon::parse($student->admission_date)->format('d M Y') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-map-marker-alt text-primary" style="width:18px"></i>
                        <small>{{ $student->user->address ?? 'N/A' }}</small>
                    </div>
                    @if($student->medical_conditions)
                        <div class="mb-2 d-flex gap-2 align-items-center">
                            <i class="fas fa-heartbeat text-danger" style="width:18px"></i>
                            <small>{{ $student->medical_conditions }}</small>
                        </div>
                    @endif
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('dashboard.students.edit', $student) }}"
                        class="btn btn-warning btn-sm text-white">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('dashboard.students.id-card', $student) }}"
                        class="btn btn-secondary btn-sm">
                        <i class="fas fa-id-card me-1"></i>ID Card
                    </a>
                    <a href="{{ route('dashboard.students.index') }}"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
        {{-- Details --}}
        <div class="col-xl-8">
            {{-- Quick Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dbeafe">
                            <i class="fas fa-clipboard-check" style="color:#2563eb"></i>
                        </div>
                        <div class="fw-bold fs-4 text-primary">{{ $student->attendances->count() }}</div>
                        <div class="stat-label">Attendance</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dcfce7">
                            <i class="fas fa-clipboard-check" style="color:#16a34a"></i>
                        </div>
                        <div class="fw-bold fs-4 text-success">
                            {{ $student->attendances->where('status','present')->count() }}
                        </div>
                        <div class="stat-label">Present</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#fef9c3">
                            <i class="fas fa-pen" style="color:#ca8a04"></i>
                        </div>
                        <div class="fw-bold fs-4 text-warning">{{ $student->marks->count() }}</div>
                        <div class="stat-label">Marks</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#fee2e2">
                            <i class="fas fa-money-bill" style="color:#dc2626"></i>
                        </div>
                        <div class="fw-bold fs-4 text-danger">
                            {{ $student->fees->where('status','pending')->count() }}
                        </div>
                        <div class="stat-label">Pending Fees</div>
                    </div>
                </div>
            </div>
            {{-- Recent Attendance --}}
            <div class="page-card mb-3">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Recent Attendance</h6>
                @if($student->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->attendances->sortByDesc('date')->take(7) as $att)
                                    <tr>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            @if($att->status === 'present')
                                                <span class="badge badge-active">Present</span>
                                            @elseif($att->status === 'absent')
                                                <span class="badge badge-inactive">Absent</span>
                                            @else
                                                <span class="badge badge-pending">{{ ucfirst($att->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $att->remarks ?? '-' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-2">No attendance records found</p>
                @endif
            </div>
            {{-- Marks --}}
            <div class="page-card mb-3">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Academic Marks</h6>
                @if($student->marks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Subject</th><th>Marks</th><th>Full</th><th>Grade</th></tr></thead>
                        <tbody>
                            @foreach($student->marks->take(8) as $mark)
                            <tr>
                                <td><small>{{ $mark->subject->name ?? '-' }}</small></td>
                                <td><small class="fw-bold">{{ $mark->marks_obtained }}</small></td>
                                <td><small>{{ $mark->full_marks }}</small></td>
                                <td><span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $mark->grade }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-2">No marks found</p>
                @endif
            </div>

            {{-- Fee Status --}}
            <div class="page-card">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Fee Status</h6>
                @if($student->fees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Month</th><th>Amount</th><th>Paid</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($student->fees->take(6) as $fee)
                            <tr>
                                <td><small>{{ $fee->month ?? '-' }}</small></td>
                                <td><small>PKR {{ number_format($fee->amount) }}</small></td>
                                <td><small class="text-success">PKR {{ number_format($fee->paid_amount) }}</small></td>
                                <td>
                                    <span class="badge {{ $fee->status === 'paid' ? 'badge-active' : 'badge-pending' }}">
                                        {{ ucfirst($fee->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted text-center py-2">No fee records found</p>
                @endif
            </div>
        </div>
    </div>
@endsection