@extends('layouts.dashboard')
@section('title', 'Notices')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notices</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Notice Board</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage school notices and announcements</p>
        </div>
        @can('create notices')
            <a href="{{ route('dashboard.notices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Notice
            </a>
        @endcan
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Published By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $i => $notice)
                        <tr id="row-n{{ $notice->id }}">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight:500;font-size:0.875rem">{{ $notice->title }}</div>
                                <small class="text-muted">{{ Str::limit($notice->content, 60) }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8">
                                    {{ ucfirst($notice->audience) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $notice->publisher->name ?? 'Admin' }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($notice->publish_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $notice->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($notice->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.notices.edit', $notice) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-n{{ $notice->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.notices.destroy', $notice) }}', 'n{{ $notice->id }}', '{{ Str::limit($notice->title, 30) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-bullhorn fa-2x mb-2 d-block"></i>
                                No notices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $notices->links() }}
    </div>
@endsection