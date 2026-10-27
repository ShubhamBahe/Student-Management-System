@extends('layouts.dashboard')
@section('title', 'Exams Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Exams Report</li>
@endsection

@section('content')
    <div class="mb-4">
        <h5 class="fw-bold mb-1" style="color:#1e293b">Examinations Report</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem">All exams and their status</p>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Academic Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $i => $exam)
                        @php
                            $days = \Carbon\Carbon::parse($exam->start_date)->diffInDays(\Carbon\Carbon::parse($exam->end_date)) + 1;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $exam->name }}</strong>
                            </td>
                            <td>
                                <small>{{ $exam->academicYear->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $days }} days</span>
                            </td>
                            <td>
                                @if($exam->status === 'upcoming')
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">Upcoming</span>
                                @elseif($exam->status === 'ongoing')
                                    <span class="badge badge-active">Ongoing</span>
                                @else
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No exams found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection