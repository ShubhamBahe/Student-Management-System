@extends('layouts.dashboard')
@section('title', 'Exams')

@section('breadcrumb')
    <li class="breadcrumb-item active">Exams</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Examinations</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage all exams and schedules</p>
        </div>
        @can('create exams')
            <a href="{{ route('dashboard.exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Exam
            </a>
        @endcan
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Academic Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $i => $exam)
                        <tr id="row-e{{ $exam->id }}">
                            <td>{{ $exams->firstItem() + $i }}</td>
                            <td><span style="font-weight:500">{{ $exam->name }}</span></td>
                            <td><small>{{ $exam->academicYear->name ?? '-' }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</small></td>
                            <td>
                                @if($exam->status === 'upcoming')
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">Upcoming</span>
                                @elseif($exam->status === 'ongoing')
                                    <span class="badge badge-active">Ongoing</span>
                                @else
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.exams.show', $exam) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                                    @can('edit exams')
                                    <a href="{{ route('dashboard.exams.edit', $exam) }}" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                                    @endcan
                                    <a href="{{ route('dashboard.marks.entry') }}?exam_id={{ $exam->id }}" class="btn btn-sm btn-success text-white" title="Enter Marks">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @can('delete exams')
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-e{{ $exam->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.exams.destroy', $exam) }}', 'e{{ $exam->id }}', '{{ $exam->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-file-alt fa-2x mb-2 d-block"></i>No exams found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $exams->links() }}
    </div>
@endsection