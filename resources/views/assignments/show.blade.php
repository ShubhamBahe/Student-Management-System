@extends('layouts.dashboard')
@section('title', 'Assignment Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.assignments.index') }}">Assignments</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Assignment Info</h6>
                <div class="mb-2"><small class="text-muted d-block">Title</small><strong>{{ $assignment->title }}</strong></div>
                <div class="mb-2"><small class="text-muted d-block">Class</small><strong>{{ $assignment->class->name ?? '-' }}</strong></div>
                <div class="mb-2"><small class="text-muted d-block">Subject</small><strong>{{ $assignment->subject->name ?? '-' }}</strong></div>
                <div class="mb-2"><small class="text-muted d-block">Due Date</small>
                    <strong class="{{ \Carbon\Carbon::parse($assignment->due_date)->isPast() ? 'text-danger' : 'text-success' }}">
                        {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                    </strong>
                </div>
                <div class="mb-2"><small class="text-muted d-block">Total Marks</small><strong>{{ $assignment->total_marks }}</strong></div>
                <div class="mb-3"><small class="text-muted d-block">Status</small>
                    <span class="badge {{ $assignment->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($assignment->status) }}
                    </span>
                </div>
                @if($assignment->description)
                    <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:0.875rem;color:#475569">
                        {{ $assignment->description }}
                    </div>
                @endif
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('dashboard.assignments.edit', $assignment) }}"
                        class="btn btn-warning btn-sm text-white">Edit
                    </a>
                    <a href="{{ route('dashboard.assignments.index') }}"
                        class="btn btn-secondary btn-sm">Back
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card">
                <h6 class="fw-bold mb-3" style="color:#1e293b">
                    Submissions ({{ $assignment->submissions->count() }})
                </h6>
                @if($assignment->submissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted</th>
                                    <th>Marks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignment->submissions as $sub)
                                    <tr>
                                        <td>
                                            <small>{{ $sub->student->user->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $sub->obtained_marks ?? '-' }} / {{ $assignment->total_marks }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $sub->status === 'graded' ? 'badge-active' : 'badge-pending' }}">
                                                {{ ucfirst($sub->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No submissions yet</p>
                @endif
            </div>
        </div>
    </div>
@endsection