@extends('layouts.dashboard')
@section('title', 'Teacher Profile')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard.teachers.index') }}">Teachers</a>
    </li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
    <div class="row g-3">
        {{-- Profile Card --}}
        <div class="col-xl-4">
            <div class="page-card text-center">
                <img src="{{ $teacher->user->photo_url }}"
                    class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;border:4px solid #dbeafe;">
                <h5 class="fw-bold mb-1" style="color:#1e293b">{{ $teacher->user->name }}</h5>
                <p class="text-muted mb-2" style="font-size:0.875rem">{{ $teacher->specialization ?? 'Teacher' }}</p>
                <span class="badge {{ $teacher->status === 'active' ? 'badge-active' : 'badge-inactive' }} mb-3">
                    {{ ucfirst($teacher->status) }}
                </span>
                <hr>
                <div class="text-start">
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-id-badge text-primary" style="width:18px"></i>
                        <small><strong>Employee ID:</strong> {{ $teacher->employee_id }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-envelope text-primary" style="width:18px"></i>
                        <small>{{ $teacher->user->email }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-phone text-primary" style="width:18px"></i>
                        <small>{{ $teacher->user->phone ?? 'N/A' }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-venus-mars text-primary" style="width:18px"></i>
                        <small>{{ ucfirst($teacher->user->gender ?? 'N/A') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-calendar text-primary" style="width:18px"></i>
                        <small>Joined: {{ \Carbon\Carbon::parse($teacher->joining_date)->format('d M Y') }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-graduation-cap text-primary" style="width:18px"></i>
                        <small>{{ $teacher->qualification }}</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-money-bill text-primary" style="width:18px"></i>
                        <small>PKR {{ number_format($teacher->salary) }}/month</small>
                    </div>
                    <div class="mb-2 d-flex gap-2 align-items-center">
                        <i class="fas fa-map-marker-alt text-primary" style="width:18px"></i>
                        <small>{{ $teacher->user->address ?? 'N/A' }}</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('dashboard.teachers.edit', $teacher) }}" class="btn btn-warning btn-sm text-white">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
        {{-- Stats + Details --}}
        <div class="col-xl-8">
            {{-- Quick Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dbeafe">
                            <i class="fas fa-school" style="color:#2563eb"></i>
                        </div>
                        <div class="fw-bold fs-4 text-primary">{{ $teacher->classes->count() }}</div>
                        <div class="stat-label">Classes</div>
                    </div>
                </div>
            <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto mb-2" style="background:#dcfce7">
                    <i class="fas fa-calendar" style="color:#16a34a"></i>
                </div>
                <div class="fw-bold fs-4 text-success">
                    {{ \Carbon\Carbon::parse($teacher->joining_date)->diffInYears(now()) }}
                </div>
                <div class="stat-label">Years Experience</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto mb-2" style="background:#fef9c3">
                    <i class="fas fa-money-bill" style="color:#ca8a04"></i>
                </div>
                <div class="fw-bold fs-4 text-warning">PKR {{ number_format($teacher->salary) }}</div>
                <div class="stat-label">Monthly Salary</div>
            </div>
        </div>
    </div>
    {{-- Classes --}}
    <div class="page-card mb-3">
        <h6 class="fw-bold mb-3" style="color:#1e293b">Assigned Classes</h6>
        @if($teacher->classes->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teacher->classes as $class)
                            <tr>
                                <td><small>{{ $class->name }}</small></td>
                                <td><span class="badge badge-active">{{ $class->students->count() }} students</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-2">No classes assigned yet</p>
        @endif
    </div>
    {{-- Teacher Info --}}
    <div class="page-card">
        <h6 class="fw-bold mb-3" style="color:#1e293b">Professional Details</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <small class="text-muted d-block">Employee ID</small>
                <strong>{{ $teacher->employee_id }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Joining Date</small>
                <strong>{{ \Carbon\Carbon::parse($teacher->joining_date)->format('d M Y') }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Qualification</small>
                <strong>{{ $teacher->qualification }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Specialization</small>
                <strong>{{ $teacher->specialization ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Salary</small>
                <strong class="text-success">PKR {{ number_format($teacher->salary) }}/month</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Status</small>
                <span class="badge {{ $teacher->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                    {{ ucfirst($teacher->status) }}
                </span>
            </div>
        </div>
    </div>
@endsection