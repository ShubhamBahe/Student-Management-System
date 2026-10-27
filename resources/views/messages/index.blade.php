@extends('layouts.dashboard')
@section('title', 'Messages')

@section('breadcrumb')
    <li class="breadcrumb-item active">Messages</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Contact Messages</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Messages received from the website contact form</p>
        </div>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $i => $msg)
                        <tr style="{{ $msg->status === 'unread' ? 'background:#fefce8;font-weight:500' : '' }}">
                            <td>{{ $messages->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                        style="width:32px;height:32px;background:#2563eb;font-size:0.75rem;min-width:32px">
                                        {{ strtoupper(substr($msg->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:0.875rem">{{ $msg->name }}</div>
                                        <small class="text-muted">{{ $msg->phone ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ $msg->email }}</small>
                            </td>
                            <td>
                                <small>{{ $msg->subject ?? 'General Inquiry' }}</small>
                            </td>
                            <td>
                                <small>{{ $msg->created_at->format('d M Y, h:i A') }}</small>
                            </td>
                            <td>
                                @if($msg->status === 'unread')
                                    <span class="badge" style="background:#fef9c3;color:#854d0e">Unread</span>
                                @else
                                    <span class="badge badge-active">Read</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.messages.show', $msg) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="mailto:{{ $msg->email }}" class="btn btn-sm btn-success text-white">
                                        <i class="fas fa-reply"></i>
                                    </a>
                                    <form id="dm-{{ $msg->id }}" method="POST" action="{{ route('dashboard.messages.destroy', $msg) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-form="dm-{{ $msg->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-envelope fa-3x mb-3 d-block" style="color:#cbd5e1"></i>
                                No messages yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $messages->links() }}
    </div>
@endsection