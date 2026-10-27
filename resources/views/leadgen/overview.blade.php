@extends('layouts.dashboard')
@section('title', 'Lead Generation')

@section('breadcrumb')
    <li class="breadcrumb-item active">Lead Generation</li>
@endsection

@section('content')
@php use App\Models\Lead; @endphp
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">Lead Generation</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem">AI agent that chats on WhatsApp and calls prospective parents</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard.leadgen.leads.import') }}" class="btn btn-outline-primary"><i class="fas fa-file-excel me-2"></i>Import</a>
        <a href="{{ route('dashboard.leadgen.leads.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Lead</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total leads', $total, 'fa-address-book', '#dbeafe', '#2563eb'],
        ['New this week', $newThisWeek, 'fa-user-plus', '#dcfce7', '#16a34a'],
        ['Conversion', $conversion . '%', 'fa-bullseye', '#fef9c3', '#ca8a04'],
        ['WhatsApp today', $waToday, 'fa-whatsapp', '#d1fae5', '#059669'],
        ['Calls today', $callsToday, 'fa-phone', '#ede9fe', '#7c3aed'],
    ] as [$label, $value, $icon, $bg, $fg])
        <div class="col-6 col-lg">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:{{ $bg }};color:{{ $fg }}">
                        <i class="{{ $icon === 'fa-whatsapp' ? 'fab' : 'fas' }} {{ $icon }}"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $value }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="page-card mb-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Pipeline</h6>
            @foreach(Lead::STATUSES as $key => [$label, $bg, $fg])
                @php $n = (int) ($byStatus[$key] ?? 0); $pct = $total ? round($n / $total * 100) : 0; @endphp
                <a href="{{ route('dashboard.leadgen.leads.index', ['status' => $key]) }}" class="text-decoration-none d-block mb-2">
                    <div class="d-flex justify-content-between" style="font-size:0.8rem;color:#475569">
                        <span>{{ $label }}</span><span class="fw-semibold">{{ $n }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $fg }}"></div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Where leads come from</h6>
            @forelse($bySource as $source => $count)
                <div class="d-flex justify-content-between py-1" style="font-size:0.875rem">
                    <span>{{ ucwords(str_replace('_', ' ', $source)) }}</span><span class="fw-semibold">{{ $count }}</span>
                </div>
            @empty
                <p class="text-muted mb-0" style="font-size:0.85rem">No leads yet.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-7">
        <div class="page-card mb-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b"><i class="fas fa-headset me-2 text-danger"></i>Needs a human</h6>
            @forelse($needsHuman as $l)
                <a href="{{ route('dashboard.leadgen.leads.show', $l) }}" class="d-flex justify-content-between align-items-center text-decoration-none py-2" style="border-bottom:1px solid #f1f5f9">
                    <div>
                        <div style="font-weight:500;color:#1e293b;font-size:0.875rem">{{ $l->name }}</div>
                        <small class="text-muted">+{{ $l->phone }}</small>
                    </div>
                    <small class="text-muted">{{ $l->updated_at->diffForHumans() }}</small>
                </a>
            @empty
                <p class="text-muted mb-0" style="font-size:0.85rem">Nothing waiting — the AI is handling everything.</p>
            @endforelse
        </div>
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Recent activity</h6>
            @forelse($recent as $m)
                <div class="d-flex gap-3 py-2" style="border-bottom:1px solid #f1f5f9">
                    <div style="width:34px;height:34px;border-radius:10px;background:{{ $m->channel === 'call' ? '#ede9fe' : '#d1fae5' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="{{ $m->channel === 'call' ? 'fas fa-phone' : 'fab fa-whatsapp' }}" style="color:{{ $m->channel === 'call' ? '#7c3aed' : '#059669' }}"></i>
                    </div>
                    <div style="min-width:0;flex:1">
                        <div style="font-size:0.85rem;font-weight:500;color:#1e293b">
                            {{ $m->lead->name ?? 'Unknown' }}
                            <span class="text-muted fw-normal">· {{ $m->direction === 'in' ? 'wrote' : ($m->by_ai ? 'AI sent' : 'staff sent') }}</span>
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.8rem">{{ \Illuminate\Support\Str::limit($m->body ?? ($m->meta['error'] ?? $m->status), 90) }}</div>
                    </div>
                    <small class="text-muted">{{ $m->created_at->diffForHumans(null, true) }}</small>
                </div>
            @empty
                <p class="text-muted mb-0" style="font-size:0.85rem">No activity yet. Add a lead or try the <a href="{{ route('dashboard.leadgen.sandbox') }}">AI test chat</a>.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
