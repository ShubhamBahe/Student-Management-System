@extends('layouts.dashboard')
@section('title', 'Outbound Calls')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Outbound Calls</li>
@endsection

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e293b">Outbound Calls</h5>
    <p class="text-muted mb-0" style="font-size:.875rem">The agent phones parents and reads your script, then takes their questions.
        Calls are only placed between {{ $window[0] }} and {{ $window[1] }}.</p>
</div>

@unless($canCall)
    <div class="alert alert-warning"><i class="fas fa-triangle-exclamation me-2"></i>No phone provider is connected yet, so calls will be saved but not dialled. You can still rehearse a script in the Voice Console.</div>
@endunless

<div class="row g-4">
    <div class="col-lg-4">
        <div class="page-card">
            <h6 class="fw-bold mb-3">Start calls</h6>
            <form method="POST" action="{{ route('dashboard.lead.calls.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Script</label>
                    <select name="script_id" class="form-select @error('script_id') is-invalid @enderror" required>
                        <option value="">Select…</option>
                        @foreach($scripts as $s)<option value="{{ $s->id }}" @selected(old('script_id') == $s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Who to call</label>
                    <select name="audience" id="aud" class="form-select">
                        <option value="selected">Students I choose</option>
                        <option value="fee_due">Everyone with fees due</option>
                        <option value="absent_streak">Absent 2+ days in a row</option>
                    </select>
                </div>
                <div class="mb-3" id="pick">
                    <label class="form-label">Students</label>
                    <select name="student_ids[]" class="form-select" multiple size="9">
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->user->name ?? '?' }} — {{ $s->class->name ?? '' }} {{ $s->section->name ?? '' }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl / Cmd to select several. Max 50 per batch.</small>
                </div>
                <button class="btn btn-primary w-100" onclick="return confirm('Place these calls now?')"><i class="fas fa-phone me-2"></i>Start calling</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="page-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>When</th><th>Student</th><th>Phone</th><th>Script</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($calls as $c)
                        @php
                            $col = ['completed' => ['#dcfce7', '#166534'], 'calling' => ['#dbeafe', '#1e40af'], 'failed' => ['#fee2e2', '#991b1b'],
                                    'no_answer' => ['#fef9c3', '#854d0e'], 'skipped' => ['#f1f5f9', '#475569'], 'queued' => ['#f1f5f9', '#475569']][$c->status] ?? ['#f1f5f9', '#475569'];
                        @endphp
                        <tr>
                            <td><small>{{ $c->created_at->format('d M, h:i A') }}</small></td>
                            <td>{{ $c->student->user->name ?? '-' }}</td>
                            <td><small>{{ $c->phone }}</small></td>
                            <td><small>{{ $c->script->name ?? '-' }}</small></td>
                            <td>
                                <span class="badge" style="background:{{ $col[0] }};color:{{ $col[1] }}">{{ str_replace('_', ' ', $c->status) }}</span>
                                @if($c->duration)<small class="text-muted">{{ $c->duration }}s</small>@endif
                                @if($c->note)<div class="small text-muted">{{ $c->note }}</div>@endif
                            </td>
                        </tr>
                        @if($c->transcript)
                            <tr><td colspan="5" class="p-0 border-0">
                                <details class="px-3 pb-2"><summary class="small text-primary" style="cursor:pointer">Conversation</summary>
                                    <div class="small mt-2" style="white-space:pre-wrap;color:#475569">{{ $c->transcript }}</div></details>
                            </td></tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No calls yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $calls->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const aud = document.getElementById('aud'), pick = document.getElementById('pick');
    aud.addEventListener('change', () => pick.style.display = aud.value === 'selected' ? '' : 'none');
</script>
@endpush
