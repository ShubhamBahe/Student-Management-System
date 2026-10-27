@extends('layouts.dashboard')
@section('title', 'Leads')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item active">Leads</li>
@endsection

@section('content')
@php use App\Models\Lead; @endphp
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">Leads</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem">Prospective parents contacted by the AI agent</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form method="POST" action="{{ route('dashboard.leadgen.leads.sync') }}">
            @csrf
            <button class="btn btn-outline-secondary" title="Import enquiries from the website admission form">
                <i class="fas fa-sync me-2"></i>Sync admission enquiries
            </button>
        </form>
        <a href="{{ route('dashboard.leadgen.leads.import') }}" class="btn btn-outline-primary"><i class="fas fa-file-excel me-2"></i>Import</a>
        <a href="{{ route('dashboard.leadgen.leads.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Lead</a>
    </div>
</div>

<div class="page-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, phone or student">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach(Lead::STATUSES as $key => [$label])
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="source" class="form-select">
                <option value="">All prospects</option>
                @foreach(['manual' => 'Added manually', 'excel' => 'Excel import', 'admission_form' => 'Website form', 'whatsapp' => 'WhatsApp inbound', 'parent' => 'Existing parents'] as $k => $v)
                    <option value="{{ $k }}" @selected(request('source') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <div class="form-check pt-2">
                <input class="form-check-input" type="checkbox" name="needs_human" value="1" id="nh" @checked(request()->boolean('needs_human')) onchange="this.form.submit()">
                <label class="form-check-label small" for="nh">Needs human</label>
            </div>
            <button class="btn btn-primary ms-auto"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<form method="POST" action="{{ route('dashboard.leadgen.leads.bulk') }}" id="bulkForm">
    @csrf
    <input type="hidden" name="action" id="bulkAction">
    <input type="hidden" name="script_id" id="bulkScript">

    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:30px"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                        <th>Lead</th>
                        <th>Student / Class</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Last contact</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($leads as $lead)
                    @php [$label, $bg, $fg] = Lead::STATUSES[$lead->status] ?? [$lead->status, '#eee', '#333']; @endphp
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="form-check-input row-check"></td>
                        <td>
                            <a href="{{ route('dashboard.leadgen.leads.show', $lead) }}" class="text-decoration-none" style="font-weight:500;color:#1e293b">{{ $lead->name }}</a>
                            @if($lead->needs_human)<span class="badge bg-danger ms-1" style="font-size:0.65rem">needs human</span>@endif
                            @if($lead->opted_out)<span class="badge bg-secondary ms-1" style="font-size:0.65rem">opted out</span>@endif
                            @unless($lead->consent)<span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem">no consent</span>@endunless
                            <div><small class="text-muted">+{{ $lead->phone }}</small></div>
                        </td>
                        <td>
                            <div>{{ $lead->student_name ?: '—' }}</div>
                            <small class="text-muted">{{ $lead->class_interest }}</small>
                        </td>
                        <td><span class="badge" style="background:{{ $bg }};color:{{ $fg }}">{{ $label }}</span></td>
                        <td><small>{{ ucwords(str_replace('_', ' ', $lead->source)) }}</small></td>
                        <td><small>{{ $lead->last_contacted_at ? $lead->last_contacted_at->diffForHumans() : '—' }}</small></td>
                        <td class="text-end">
                            <a href="{{ route('dashboard.leadgen.leads.show', $lead) }}" class="btn btn-sm btn-light"><i class="fas fa-comments"></i></a>
                            <a href="{{ route('dashboard.leadgen.leads.edit', $lead) }}" class="btn btn-sm btn-light"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No leads yet. Add one, import an Excel sheet, or sync website admission enquiries.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2" style="border-top:1px solid #f1f5f9">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small text-muted">With selected:</span>
                <select id="waScript" class="form-select form-select-sm" style="width:auto">
                    <option value="">WhatsApp: default script</option>
                    @foreach($whatsapp as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
                <button type="button" class="btn btn-sm btn-success" onclick="bulkSend('whatsapp')"><i class="fab fa-whatsapp me-1"></i>Send WhatsApp</button>
                <select id="callScript" class="form-select form-select-sm" style="width:auto">
                    <option value="">Call: default script</option>
                    @foreach($calls as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
                <button type="button" class="btn btn-sm btn-primary" onclick="bulkSend('call')"><i class="fas fa-phone me-1"></i>AI Call</button>
            </div>
            {{ $leads->links() }}
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
    });
    function bulkSend(action) {
        const count = document.querySelectorAll('.row-check:checked').length;
        if (!count) { Swal.fire('Select leads first', 'Tick at least one lead.', 'info'); return; }
        Swal.fire({
            title: action === 'call' ? `Place ${count} AI call(s)?` : `Send WhatsApp to ${count} lead(s)?`,
            text: 'Leads that opted out or have no consent are skipped automatically.',
            icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, go ahead'
        }).then(r => {
            if (!r.isConfirmed) return;
            document.getElementById('bulkAction').value = action;
            document.getElementById('bulkScript').value = document.getElementById(action === 'call' ? 'callScript' : 'waScript').value;
            document.getElementById('bulkForm').submit();
        });
    }
</script>
@endpush
