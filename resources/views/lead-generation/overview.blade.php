@extends('layouts.dashboard')
@section('title', 'AI Voice Agent')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">AI Voice Agent</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">AI Voice Agent for Parents</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem">Answers parents on WhatsApp and phone in Hindi, Hinglish or English, and calls them with your scripts.</p>
    </div>
    <a href="{{ route('dashboard.lead.console') }}" class="btn btn-primary"><i class="fas fa-microphone-lines me-2"></i>Open Voice Console</a>
</div>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Parent contacts', $stats['guardians'], 'fa-address-book', '#2563eb', '#dbeafe'],
            ['Questions today', $stats['queries'], 'fa-comments', '#16a34a', '#dcfce7'],
            ['System issues today', $stats['errors'], 'fa-triangle-exclamation', '#d97706', '#fef3c7'],
            ['Numbers blocked now', $stats['blocked'], 'fa-ban', '#dc2626', '#fee2e2'],
            ['Open owner alerts', $stats['alerts'], 'fa-bell', '#7c3aed', '#ede9fe'],
            ['Calls today', $stats['calls'], 'fa-phone-volume', '#0891b2', '#cffafe'],
        ];
    @endphp
    @foreach($cards as [$label, $n, $icon, $fg, $bg])
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="stat-icon mb-2" style="background:{{ $bg }};color:{{ $fg }}"><i class="fas {{ $icon }}"></i></div>
                <div class="stat-number">{{ $n }}</div>
                <div class="stat-label">{{ $label }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="page-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color:#1e293b">Recent questions</h6>
                <a href="{{ route('dashboard.lead.logs') }}" class="small">Full log</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>When</th><th>Phone</th><th>Question</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recent as $r)
                        <tr>
                            <td><small>{{ $r->created_at->format('d M, h:i A') }}</small></td>
                            <td><small>{{ $r->phone }}</small></td>
                            <td style="max-width:280px"><small>{{ \Illuminate\Support\Str::limit($r->question, 70) }}</small></td>
                            <td>@include('lead-generation.partials.status', ['status' => $r->status])</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No questions yet. Try the Voice Console.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($alerts->count())
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Needs the owner</h6>
            @foreach($alerts as $a)
                <div class="d-flex gap-3 py-2 border-bottom">
                    <span class="badge align-self-start" style="background:#fee2e2;color:#991b1b">{{ str_replace('_', ' ', $a->type) }}</span>
                    <div class="small"><div class="fw-semibold">{{ $a->title }}</div>
                        <div class="text-muted">{{ \Illuminate\Support\Str::limit($a->details, 140) }}</div></div>
                </div>
            @endforeach
            <a href="{{ route('dashboard.lead.logs', ['tab' => 'alerts']) }}" class="btn btn-sm btn-outline-danger mt-3">Open alerts</a>
        </div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="page-card mb-4">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Connections</h6>
            @php
                $rows = [
                    ['AI answers (Claude)', $integrations['ai'], 'Set ANTHROPIC_API_KEY in .env. Without it the agent runs in built-in rule-based mode (still fully working).'],
                    ['WhatsApp / SMS provider (Twilio)', $integrations['twilio'], 'Set TWILIO_SID and TWILIO_TOKEN in .env.'],
                    ['Outbound phone calls', $integrations['calls'], 'Set TWILIO_VOICE_FROM in .env.'],
                    ['Owner alert number', $integrations['owner'], 'Add it under Agent Settings.'],
                ];
            @endphp
            @foreach($rows as [$label, $ok, $hint])
                <div class="d-flex gap-2 py-2 border-bottom">
                    <i class="fas {{ $ok ? 'fa-circle-check text-success' : 'fa-circle-minus text-secondary' }} mt-1"></i>
                    <div class="small"><div class="fw-semibold">{{ $label }} — {{ $ok ? 'connected' : 'not set' }}</div>
                        @unless($ok)<div class="text-muted">{{ $hint }}</div>@endunless</div>
                </div>
            @endforeach
        </div>

        <div class="page-card">
            <h6 class="fw-bold mb-2" style="color:#1e293b">Webhook URLs</h6>
            <p class="small text-muted">Paste these into the Twilio console (HTTP POST). They need a public https address (APP_URL).</p>
            @foreach($webhooks as $label => $url)
                <label class="form-label small mb-1">{{ $label }}</label>
                <input class="form-control form-control-sm mb-2" readonly value="{{ $url }}" onclick="this.select()">
            @endforeach
        </div>
    </div>
</div>
@endsection
