@extends('layouts.dashboard')
@section('title', 'Notifications')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Notifications</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">All your system notifications</p>
        </div>
        @if($notifications->count() > 0)
            <form method="POST" action="{{ route('dashboard.notifications.destroy-all') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash me-1"></i>Clear All
                </button>
            </form>
        @endif
    </div>
    <div class="page-card">
        @forelse($notifications as $notif)
            <div class="d-flex gap-3 p-3 rounded-3 mb-2" id="row-no{{ $notif->id }}"
                style="background:{{ $notif->is_read ? '#fff' : '#f0f9ff' }};border:1px solid {{ $notif->is_read ? '#e2e8f0' : '#bfdbfe' }}">
                {{-- Icon --}}
                <div style="width:48px;height:48px;border-radius:12px;
                            background:{{ $notif->color }}20;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="{{ $notif->icon }}" style="color:{{ $notif->color }};font-size:1.1rem"></i>
                </div>
                {{-- Content --}}
                <div style="flex:1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-weight:{{ $notif->is_read ? '400' : '600' }};color:#1e293b;font-size:0.9rem">
                                {{ $notif->title }}
                                @if(!$notif->is_read)
                                <span class="badge ms-1" style="background:#2563eb;font-size:0.65rem">New</span>
                                @endif
                            </div>
                            <div style="color:#64748b;font-size:0.8rem;margin-top:2px">
                                {{ $notif->message }}
                            </div>
                            <div style="color:#94a3b8;font-size:0.75rem;margin-top:4px">
                                <i class="fas fa-clock me-1"></i>
                                {{ $notif->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="d-flex gap-2 ms-3">
                            @if($notif->url)
                                <a href="{{ $notif->url }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem">
                                    View
                                </a>
                            @endif
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    id="del-btn-no{{ $notif->id }}"
                                    style="font-size:0.75rem"
                                    onclick="ajaxDelete('{{ route('dashboard.notifications.destroy', $notif->id) }}', 'no{{ $notif->id }}', '{{ Str::limit($notif->title, 30) }}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x mb-3 d-block" style="color:#cbd5e1"></i>
                <h6 class="text-muted">No notifications yet</h6>
                <p class="text-muted" style="font-size:0.875rem">You are all caught up!</p>
            </div>
        @endforelse
        <div class="mt-3">{{ $notifications->links() }}</div>
    </div>
@endsection