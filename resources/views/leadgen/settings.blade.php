@extends('layouts.dashboard')
@section('title', 'AI Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item active">AI Settings</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="page-card">
            <h6 class="fw-bold mb-4" style="color:#1e293b">Agent behaviour</h6>

            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ route('dashboard.leadgen.settings.save') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Agent name</label>
                        <input name="leadgen_agent_name" class="form-control" value="{{ old('leadgen_agent_name', $settings['leadgen_agent_name'] ?? 'School Assistant') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">First thing said on calls</label>
                        <input name="leadgen_call_first_message" class="form-control"
                               value="{{ old('leadgen_call_first_message', $settings['leadgen_call_first_message'] ?? 'Hello, may I speak with {name}? This is {agent} calling from {school}.') }}">
                        <div class="form-text">Placeholders: {name} {agent} {school}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">School knowledge <span class="text-muted">(the agent answers only from this and from your database)</span></label>
                        <textarea name="leadgen_knowledge" rows="10" class="form-control"
placeholder="Admission process and documents needed&#10;Fee structure per class&#10;School timings, transport, facilities&#10;Curriculum / board, languages&#10;Admission dates, visit hours&#10;Common FAQs and their answers">{{ old('leadgen_knowledge', $settings['leadgen_knowledge'] ?? '') }}</textarea>
                        <div class="form-text">School name, address, phone and email are added automatically from General Settings.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Extra instructions <span class="text-muted">(tone, things to avoid, languages)</span></label>
                        <textarea name="leadgen_instructions" rows="3" class="form-control"
placeholder="e.g. Always greet with Namaste. Never discuss fee discounts — refer to the principal.">{{ old('leadgen_instructions', $settings['leadgen_instructions'] ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="leadgen_enabled" value="1" id="en" @checked(($settings['leadgen_enabled'] ?? '1') === '1')>
                            <label class="form-check-label" for="en"><strong>AI auto-replies on WhatsApp</strong> — when off, new messages are flagged "needs a human"</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="leadgen_auto_welcome" value="1" id="aw" @checked(($settings['leadgen_auto_welcome'] ?? '0') === '1')>
                            <label class="form-check-label" for="aw"><strong>Auto-welcome new website admission enquiries</strong> on WhatsApp (uses your default WhatsApp script)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="leadgen_auto_followup" value="1" id="af" @checked(($settings['leadgen_auto_followup'] ?? '0') === '1')>
                            <label class="form-check-label" for="af"><strong>Automatic follow-ups</strong> when a lead's follow-up date arrives</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="page-card mb-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Connection status</h6>
            @foreach($checks as $label => $ok)
                <div class="d-flex justify-content-between py-1" style="font-size:0.85rem">
                    <span>{{ $label }}</span>
                    @if($ok)<span class="text-success"><i class="fas fa-check-circle"></i> Ready</span>
                    @else<span class="text-danger"><i class="fas fa-times-circle"></i> Missing</span>@endif
                </div>
            @endforeach
            <p class="text-muted small mt-3 mb-0">Keys are set in the server's <code>.env</code> file, never in this page.</p>
        </div>

        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Webhook URLs</h6>
            <div class="small text-muted">WhatsApp (Meta developer console → Callback URL)</div>
            <input class="form-control form-control-sm mb-2" readonly value="{{ route('webhooks.whatsapp') }}" onclick="this.select()">
            <div class="small text-muted">Voice (Vapi → Server URL)</div>
            <input class="form-control form-control-sm mb-2" readonly value="{{ route('webhooks.voice') }}" onclick="this.select()">
            <p class="text-muted small mb-0">Must be public HTTPS URLs. On localhost use a tunnel such as ngrok.</p>
        </div>
    </div>
</div>
@endsection
