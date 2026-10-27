@extends('layouts.dashboard')
@section('title', 'Assignments')

@section('breadcrumb')
    <li class="breadcrumb-item active">Assignments</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Assignments</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage homework and assignments</p>
        </div>
        @can('create assignments')
            <a href="{{ route('dashboard.assignments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Assignment
            </a>
        @endcan
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Total Marks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $i => $assignment)
                        <tr id="row-a{{ $assignment->id }}">
                            <td>{{ $assignments->firstItem() + $i }}</td>
                            <td>
                                <div style="font-weight:500;font-size:0.875rem">{{ $assignment->title }}</div>
                                @if($assignment->description)
                                    <small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ $assignment->class->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $assignment->subject->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="{{ \Carbon\Carbon::parse($assignment->due_date)->isPast() ? 'text-danger' : 'text-success' }}">
                                    {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $assignment->total_marks }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $assignment->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.assignments.show', $assignment) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.assignments.edit', $assignment) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        id="del-btn-a{{ $assignment->id }}"
                                        onclick="ajaxDelete('{{ route('dashboard.assignments.destroy', $assignment) }}', 'a{{ $assignment->id }}', '{{ $assignment->title }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-tasks fa-2x mb-2 d-block"></i>
                                No assignments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $assignments->links() }}
    </div>
@endsection