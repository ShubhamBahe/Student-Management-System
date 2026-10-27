@extends('layouts.dashboard')
@section('title', $lead->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">{{ $lead->name }}</li>
@endsection

@push('styles')
<style>
    .chat-box { height: 460px; overflow-y: auto; background: #efeae2; border-radius: 12px; padding: 16px; }
    .bubble { max-width: 78%; padding: 8px 12px; border-radius: 10px; margin-bottom: 8px; font-size: 0.85rem; box-shadow: 0 1px 1px rgba(0,0,0,.08); white-space: pre-wrap; word-wrap: break-word; }
    .bubble.in  { background: #fff; margin-right: auto; }
    .bubble.out { background: #d9fdd3; margin-left: auto; }
    .bubble.out.human { background: #dbeafe; }
    .bubble .meta { font-size: 0.68rem; color: #667781; margin-top: 3px; display: flex; gap: 6px; justify-content: flex-end; }
    .call-card { background: #fff; border-left: 4px solid #7c3aed; border-radius: 8px; padding: 10px 12px; margin: 10px 0; font-size: 0.82rem; }
</style>
@endpush

@section('content')
@php use App\Models\Lead; [$label, $bg, $fg] = Lead::STATUSES[$lead->status] ?? [$lead->status, '#eee', '#333']; @endphp

<div class="row g-3">
    {{-- LEFT: details + actions --}}
    <div class="col-lg-4">
        <div class="page-card mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-bold mb-1" style="color:#1e293b">{{ $lead->name }}</h6>
                    <div class="text-muted small">+{{ $lead->phone }}</div>
                </div>
                <span class="badge" style="background:{{ $bg }};color:{{ $fg }}">{{ $label }}</span>
            </div>
            <hr>
            <dl class="row mb-0" style="font-size:0.85rem">
                <dt class="col-5 text-muted fw-normal">Child</dt><dd class="col-7">{{ $lead->student_name ?: '—' }}</dd>
                <dt class="col-5 text-muted fw-normal">Class</dt><dd class="col-7">{{ $lead->class_interest ?: '—' }}</dd>
                <dt class="col-5 text-muted fw-normal">Email</dt><dd class="col-7 text-break">{{ $lead->email ?: '—' }}</dd>
                <dt class="col-5 text-muted fw-normal">Source</dt><dd class="col-7">{{ ucwords(str_replace('_', ' ', $lead->source)) }}</dd>
                <dt class="col-5 text-muted fw-normal">Follow-up</dt><dd class="col-7">{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('d M Y, H:i') : '—' }}</dd>
                <dt class="col-5 text-muted fw-normal">Last contact</dt><dd class="col-7">{{ $lead->last_contacted_at ? $lead->last_contacted_at->diffForHumans() : '—' }}</dd>
            </dl>
            @if($lead->notes)
                <hr>
                <div class="small text-muted mb-1">Notes</div>
                <div style="font-size:0.82rem;white-space:pre-wrap">{{ $lead->notes }}</div>
            @endif
            <hr>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('dashboard.leadgen.leads.edit', $lead) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit</a>
                <form method="POST" action="{{ route('dashboard.leadgen.leads.destroy', $lead) }}" id="del-lead">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-form="del-lead"><i class="fas fa-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>

        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">AI agent</h6>

            @if($lead->opted_out)
                <div class="alert alert-secondary py-2 small">This lead replied STOP and will not be contacted.</div>
            @elseif(!$lead->consent)
                <div class="alert alert-warning py-2 small">No consent recorded — contacting is disabled. Tick consent in Edit if they agreed.</div>
            @endif

            @if($lead->needs_human)
                <div class="alert alert-danger py-2 small d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-headset me-1"></i>Needs a human</span>
                    <form method="POST" action="{{ route('dashboard.leadgen.leads.resolve', $lead) }}">@csrf
                        <button class="btn btn-sm btn-light">Mark handled</button>
                    </form>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.leadgen.leads.toggle-ai', $lead) }}" class="mb-3">
                @csrf
                <button class="btn btn-sm {{ $lead->ai_enabled ? 'btn-success' : 'btn-outline-secondary' }} w-100">
                    <i class="fas fa-robot me-1"></i>
                    {{ $lead->ai_enabled ? 'AI replies are ON — click to take over' : 'AI replies are OFF — click to turn on' }}
                </button>
            </form>

            @if($lead->canBeContacted())
                <form method="POST" action="{{ route('dashboard.leadgen.leads.call', $lead) }}" class="d-flex gap-2">
                    @csrf
                    <select name="script_id" class="form-select form-select-sm">
                        <option value="">Default call script</option>
                        @foreach($calls as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                    <button class="btn btn-sm btn-primary text-nowrap"><i class="fas fa-phone me-1"></i>AI Call</button>
                </form>
            @endif
        </div>
    </div>

    {{-- RIGHT: conversation --}}
    <div class="col-lg-8">
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Conversation</h6>

            <div class="chat-box" id="chatBox">
                @forelse($messages as $m)
                    @if($m->channel === 'call')
                        <div class="call-card">
                            <div class="fw-semibold"><i class="fas fa-phone me-1" style="color:#7c3aed"></i>AI call
                                <span class="text-muted fw-normal">· {{ $m->created_at->format('d M, H:i') }} · {{ $m->status }}</span></div>
                            @if(!empty($m->meta['summary']))<div class="mt-1">{{ $m->meta['summary'] }}</div>@endif
                            @if(!empty($m->meta['error']))<div class="text-danger mt-1">{{ $m->meta['error'] }}</div>@endif
                            @if($m->body && $m->body !== 'Call started…')
                                <details class="mt-1"><summary class="text-primary" style="cursor:pointer">Transcript</summary>
                                    <div class="mt-1" style="white-space:pre-wrap">{{ $m->body }}</div></details>
                            @endif
                            @if(!empty($m->meta['recording']))<a href="{{ $m->meta['recording'] }}" target="_blank" rel="noopener" class="small">Recording</a>@endif
                        </div>
                    @else
                        <div class="d-flex">
                            <div class="bubble {{ $m->direction }} {{ $m->direction === 'out' && !$m->by_ai ? 'human' : '' }}">
                                {{ $m->body }}
                                <div class="meta">
                                    @if($m->direction === 'out')<span>{{ $m->by_ai ? 'AI' : 'Staff' }}</span>@endif
                                    <span>{{ $m->created_at->format('d M H:i') }}</span>
                                    @if($m->direction === 'out')
                                        <span class="{{ $m->status === 'failed' ? 'text-danger fw-bold' : '' }}">{{ $m->status }}</span>
                                    @endif
                                </div>
                                @if(!empty($m->meta['error']))<div class="text-danger" style="font-size:0.72rem">{{ $m->meta['error'] }}</div>@endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center text-muted pt-5">No messages yet.</div>
                @endforelse
            </div>

            @if($lead->canBeContacted())
                <form method="POST" action="{{ route('dashboard.leadgen.leads.message', $lead) }}" class="mt-3">
                    @csrf
                    <div class="input-group">
                        <textarea name="text" rows="2" class="form-control" placeholder="Reply as staff (this pauses the AI for this lead)…" required></textarea>
                        <button class="btn btn-success"><i class="fas fa-paper-plane"></i></button>
                    </div>
                    <div class="form-text">Outside WhatsApp's 24-hour window, free text cannot be delivered — the approved template is sent instead.</div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const box = document.getElementById('chatBox'); box.scrollTop = box.scrollHeight;
</script>
@endpush
