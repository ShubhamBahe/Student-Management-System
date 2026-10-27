@extends('layouts.dashboard')
@section('title', 'Events')

@section('breadcrumb')
    <li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">School Events</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage all school events and activities</p>
        </div>
        <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Event
        </a>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event</th>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $i => $event)
                        <tr id="row-ev{{ $event->id }}">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:500;font-size:0.875rem">{{ $event->title }}</div>
                                <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                            </td>
                            <td><small><i class="fas fa-map-marker-alt text-primary me-1"></i>{{ $event->location ?? '-' }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</small></td>
                            <td><small>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('d M Y') : '-' }}</small></td>
                            <td>
                                <span class="badge {{ $event->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.events.edit', $event) }}"
                                    class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-ev{{ $event->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.events.destroy', $event) }}', 'ev{{ $event->id }}', '{{ $event->title }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>   
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-calendar fa-2x mb-2 d-block"></i>
                                No events found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $events->links() }}
    </div>
@endsection