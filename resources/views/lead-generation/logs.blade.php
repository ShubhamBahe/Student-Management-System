@extends('layouts.dashboard')
@section('title', 'Query Log & Alerts')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Query Log &amp; Alerts</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">Query Log &amp; Owner Alerts</h5>
        <p class="text-muted mb-0" style="font-size:.875rem">Every question and reply, plus everything the agent passed to the owner.</p>
    </div>
    <a href="{{ route('dashboard.lead.logs.export') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-csv me-1"></i>Export CSV</a>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link {{ $tab === 'queries' ? 'active' : '' }}" href="{{ route('dashboard.lead.logs') }}">Query Log</a></li>
    <li class="nav-item"><a class="nav-link {{ $tab === 'alerts' ? 'active' : '' }}" href="{{ route('dashboard.lead.logs', ['tab' => 'alerts']) }}">Owner Alerts
        @if($newAlerts)<span class="badge bg-danger ms-1">{{ $newAlerts }}</span>@endif</a></li>
    <li class="nav-item"><a class="nav-link {{ $tab === 'blocked' ? 'active' : '' }}" href="{{ route('dashboard.lead.logs', ['tab' => 'blocked']) }}">Blocked Numbers
        @if($blocked->count())<span class="badge bg-secondary ms-1">{{ $blocked->count() }}</span>@endif</a></li>
</ul>

<div class="page-card">
@if($tab === 'queries')
    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-4"><input name="search" class="form-control" placeholder="Search phone, question or reply" value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select"><option value="">Any status</option>
                @foreach(['ok', 'blocked', 'escalated', 'needs_text', 'unregistered', 'clarify', 'error'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ str_replace('_', ' ', $s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="channel" class="form-select"><option value="">Any channel</option>
                @foreach(['web', 'whatsapp', 'voice', 'outbound'] as $c)<option value="{{ $c }}" @selected(request('channel') === $c)>{{ $c }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Time</th><th>Phone / channel</th><th>Question</th><th>Reply</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($logs as $l)
                <tr>
                    <td><small>{{ $l->created_at->format('d M, h:i A') }}</small></td>
                    <td><small>{{ $l->phone }}<br><span class="text-muted">{{ $l->channel }} · {{ $l->language }}</span></small></td>
                    <td style="max-width:240px"><small>{{ $l->question }}</small></td>
                    <td style="max-width:340px">
                        <details><summary class="small text-muted" style="cursor:pointer">{{ \Illuminate\Support\Str::limit($l->reply, 70) }}</summary>
                            <div class="small mt-1" style="white-space:pre-wrap">{{ $l->reply }}</div></details>
                        @if($l->error)<div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($l->error, 120) }}</div>@endif
                    </td>
                    <td>@include('lead-generation.partials.status', ['status' => $l->status])</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nothing logged yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}

@elseif($tab === 'alerts')
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Time</th><th>Type</th><th>Details</th><th>Sent to owner</th><th></th></tr></thead>
            <tbody>
            @forelse($alerts as $a)
                <tr style="{{ $a->status === 'new' ? 'background:#fefce8' : '' }}">
                    <td><small>{{ $a->created_at->format('d M, h:i A') }}</small></td>
                    <td><span class="badge" style="background:#ede9fe;color:#5b21b6">{{ str_replace('_', ' ', $a->type) }}</span></td>
                    <td style="max-width:420px"><div class="fw-semibold small">{{ $a->title }}</div>
                        <div class="small text-muted" style="white-space:pre-wrap">{{ $a->details }}</div>
                        <small>{{ $a->phone }} {{ $a->sender_name ? '· ' . $a->sender_name : '' }}</small></td>
                    <td>{!! $a->forwarded ? '<i class="fas fa-check text-success"></i>' : '<small class="text-muted">panel only</small>' !!}</td>
                    <td>
                        <form method="POST" action="{{ route('dashboard.lead.alerts.toggle', $a) }}">@csrf
                            <button class="btn btn-sm {{ $a->status === 'new' ? 'btn-success' : 'btn-outline-secondary' }}">{{ $a->status === 'new' ? 'Mark handled' : 'Reopen' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No alerts.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $alerts->links() }}

@else
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Phone</th><th>Failed attempts</th><th>Blocked until</th><th></th></tr></thead>
            <tbody>
            @forelse($blocked as $b)
                <tr><td>{{ $b->phone }}</td><td>{{ $b->failed_count }}</td><td>{{ $b->blocked_until->format('d M, h:i A') }}</td>
                    <td><form method="POST" action="{{ route('dashboard.lead.blocked.destroy', $b) }}">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Unblock</button></form></td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No numbers are blocked right now.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endif
</div>
@endsection
