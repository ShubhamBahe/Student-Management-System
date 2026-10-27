@extends('layouts.dashboard')
@section('title', 'Agent Settings')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Agent Settings</li>
@endsection

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e293b">Agent Settings</h5>
    <p class="text-muted mb-0" style="font-size:.875rem">Secrets (API keys) stay in your <code>.env</code> file; everything else is here.</p>
</div>

<form method="POST" action="{{ route('dashboard.lead.settings.update') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-7">
        <div class="page-card mb-4">
            <h6 class="fw-bold mb-3">Owner</h6>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Owner name</label>
                    <input name="owner_name" class="form-control" value="{{ old('owner_name', $v['owner_name']) }}"></div>
                <div class="col-md-6"><label class="form-label">Owner WhatsApp / phone</label>
                    <input name="owner_phone" class="form-control" value="{{ old('owner_phone', $v['owner_phone']) }}" placeholder="98765 43210">
                    <small class="text-muted">Complaints, refunds, legal messages and unknown-ID alerts are sent here.</small></div>
            </div>
        </div>
        <div class="page-card mb-4">
            <h6 class="fw-bold mb-3">Security</h6>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Failed attempts before blocking</label>
                    <input type="number" min="1" max="10" name="max_failed_attempts" class="form-control" value="{{ old('max_failed_attempts', $v['max_failed_attempts']) }}"></div>
                <div class="col-md-6"><label class="form-label">Block duration (minutes)</label>
                    <input type="number" min="1" max="1440" name="block_minutes" class="form-control" value="{{ old('block_minutes', $v['block_minutes']) }}"></div>
            </div>
        </div>
        <div class="page-card">
            <h6 class="fw-bold mb-3">Outbound call hours</h6>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">From</label>
                    <input type="time" name="call_start" class="form-control" value="{{ old('call_start', $v['call_start']) }}"></div>
                <div class="col-md-6"><label class="form-label">Until</label>
                    <input type="time" name="call_end" class="form-control" value="{{ old('call_end', $v['call_end']) }}"></div>
            </div>
            <small class="text-muted">Uses the app timezone: <strong>{{ $tz }}</strong> (change <code>APP_TIMEZONE</code> in .env if this is not your local time).</small>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="page-card mb-4">
            <h6 class="fw-bold mb-3">AI answers</h6>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="ai_enabled" value="1" id="ai" @checked($v['ai_enabled'] === '1')>
                <label class="form-check-label" for="ai">Use AI to word the answers</label>
            </div>
            <p class="small text-muted mb-1">API key: <strong>{{ $aiKey ? 'found in .env' : 'not set' }}</strong> · Model: <code>{{ $model }}</code></p>
            <p class="small text-muted mb-0">All numbers always come from your database. If the AI fails, parents get a polite "system issue" message and the failure is logged.</p>
        </div>
        <div class="page-card">
            <h6 class="fw-bold mb-3">Phone &amp; WhatsApp</h6>
            <p class="small mb-1"><i class="fas {{ $twilio ? 'fa-circle-check text-success' : 'fa-circle-minus text-secondary' }} me-1"></i>Provider credentials {{ $twilio ? 'found' : 'not set' }}</p>
            <p class="small mb-0"><i class="fas {{ $canCall ? 'fa-circle-check text-success' : 'fa-circle-minus text-secondary' }} me-1"></i>Outbound calling {{ $canCall ? 'ready' : 'not set up' }}</p>
        </div>
    </div>
</div>
<button class="btn btn-primary mt-4 px-4">Save settings</button>
</form>
@endsection
