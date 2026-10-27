@extends('layouts.dashboard')
@section('title', 'Exam Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-4" style="color:#1e293b">Exam Information</h6>
                <div class="mb-3">
                    <small class="text-muted d-block">Exam Name</small>
                    <strong>{{ $exam->name }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Academic Year</small>
                    <strong>{{ $exam->academicYear->name ?? '-' }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Start Date</small>
                    <strong>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">End Date</small>
                    <strong>{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Duration</small>
                    <strong>
                        {{ \Carbon\Carbon::parse($exam->start_date)->diffInDays(\Carbon\Carbon::parse($exam->end_date)) + 1 }} days
                    </strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    @if($exam->status === 'upcoming')
                        <span class="badge" style="background:#dbeafe;color:#1d4ed8">Upcoming</span>
                    @elseif($exam->status === 'ongoing')
                        <span class="badge badge-active">Ongoing</span>
                    @else
                        <span class="badge bg-secondary">Completed</span>
                    @endif
                </div>
                @if($exam->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <p style="font-size:0.875rem;color:#475569">{{ $exam->description }}</p>
                </div>
                @endif
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('dashboard.exams.edit', $exam) }}"
                    class="btn btn-warning btn-sm text-white">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('dashboard.marks.entry') }}?exam_id={{ $exam->id }}"
                    class="btn btn-success btn-sm text-white">
                        <i class="fas fa-pen me-1"></i>Enter Marks
                    </a>
                    <a href="{{ route('dashboard.exams.index') }}"
                    class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            {{-- Summary Cards --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dbeafe">
                            <i class="fas fa-pen" style="color:#2563eb"></i>
                        </div>
                        <div class="fw-bold fs-4 text-primary">{{ $exam->marks->count() }}</div>
                        <div class="stat-label">Total Marks Records</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dcfce7">
                            <i class="fas fa-user-graduate" style="color:#16a34a"></i>
                        </div>
                        <div class="fw-bold fs-4 text-success">
                            {{ $exam->marks->pluck('student_id')->unique()->count() }}
                        </div>
                        <div class="stat-label">Students Appeared</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#fef9c3">
                            <i class="fas fa-percentage" style="color:#ca8a04"></i>
                        </div>
                        <div class="fw-bold fs-4 text-warning">
                            @if($exam->marks->count() > 0)
                                {{ round($exam->marks->avg(fn($m) => ($m->marks_obtained / $m->full_marks) * 100), 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="stat-label">Average Score</div>
                    </div>
                </div>
            </div>
            {{-- Marks Records --}}
            <div class="page-card">
                <h6 class="fw-bold mb-3" style="color:#1e293b">
                    Marks Records ({{ $exam->marks->count() }})
                </h6>
                @if($exam->marks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Full</th>
                                <th>%</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam->marks->take(20) as $i => $mark)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <small style="font-weight:500">
                                        {{ $mark->student->user->name ?? '-' }}
                                    </small>
                                </td>
                                <td><small>{{ $mark->subject->name ?? '-' }}</small></td>
                                <td><small class="fw-bold">{{ $mark->marks_obtained }}</small></td>
                                <td><small>{{ $mark->full_marks }}</small></td>
                                <td>
                                    <small>
                                        {{ $mark->full_marks > 0 ? round(($mark->marks_obtained/$mark->full_marks)*100, 1) : 0 }}%
                                    </small>
                                </td>
                                <td>
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">
                                        {{ $mark->grade }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($exam->marks->count() > 20)
                <p class="text-muted text-center mt-2" style="font-size:0.8rem">
                    Showing 20 of {{ $exam->marks->count() }} records
                </p>
                @endif
                @else
                <p class="text-muted text-center py-4">
                    <i class="fas fa-pen fa-2x mb-2 d-block" style="color:#cbd5e1"></i>
                    No marks entered yet.
                    <a href="{{ route('dashboard.marks.entry') }}?exam_id={{ $exam->id }}">
                        Enter marks now
                    </a>
                </p>
                @endif
            </div>
        </div>
    </div>
@endsection