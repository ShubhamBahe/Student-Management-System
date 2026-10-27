@extends('layouts.dashboard')
@section('title', 'View Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.messages.index') }}">Messages</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="page-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h6 class="fw-bold mb-0" style="color:#1e293b">Message Details</h6>
                    <a href="{{ route('dashboard.messages.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">From</small>
                            <strong>{{ $message->name }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $message->email }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $message->phone ?? 'Not provided' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">Subject</small>
                            <strong>{{ $message->subject ?? 'General Inquiry' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">Date</small>
                            <strong>{{ $message->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge badge-active">Read</span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <small class="text-muted d-block mb-2 fw-600">Message</small>
                    <div class="p-4 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;line-height:1.8;color:#374151">
                        {{ $message->message }}
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}"
                        class="btn btn-success">
                        <i class="fas fa-reply me-2"></i>Reply via Email
                    </a>
                    <form id="del-msg" method="POST" action="{{ route('dashboard.messages.destroy', $message) }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-danger delete-btn" data-form="del-msg">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection