@extends('layouts.dashboard')
@section('title', 'Call Scripts')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Call Scripts</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">Call Scripts</h5>
        <p class="text-muted mb-0" style="font-size:.875rem">The agent reads your script to the parent, then answers any follow-up question. Use placeholders to personalise each call.</p>
    </div>
    <form method="POST" action="{{ route('dashboard.lead.scripts.samples') }}">@csrf
        <button class="btn btn-outline-primary btn-sm">Add sample scripts</button>
    </form>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="page-card">
            <h6 class="fw-bold mb-3">{{ $edit ? 'Edit script' : 'New script' }}</h6>
            <form method="POST" action="{{ $edit ? route('dashboard.lead.scripts.update', $edit) : route('dashboard.lead.scripts.store') }}">
                @csrf @if($edit) @method('PUT') @endif
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $edit->name ?? '') }}" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Purpose</label>
                        <select name="purpose" class="form-select">
                            @foreach(['general' => 'General', 'fee_reminder' => 'Fee reminder', 'attendance' => 'Attendance', 'ptm' => 'Parent meeting', 'marks' => 'Marks'] as $k => $v)
                                <option value="{{ $k }}" @selected(old('purpose', $edit->purpose ?? 'general') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Language</label>
                        <select name="language" class="form-select">
                            @foreach(['hinglish' => 'Hinglish', 'hi' => 'Hindi', 'en' => 'English'] as $k => $v)
                                <option value="{{ $k }}" @selected(old('language', $edit->language ?? 'hinglish') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Script</label>
                    <textarea name="body" rows="9" class="form-control @error('body') is-invalid @enderror" required
                        placeholder="Namaste {parent_name}, main {school_name} se bol raha hoon…">{{ old('body', $edit->body ?? '') }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Leave a blank line to make the agent pause between parts.</small>
                </div>
                <div class="mb-3 small">
                    @foreach($placeholders as $p)<code class="me-1">{{ $p }}</code>@endforeach
                </div>
                @if($edit)
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="sa" @checked($edit->is_active)>
                        <label class="form-check-label" for="sa">Active</label>
                    </div>
                @endif
                <button class="btn btn-primary">{{ $edit ? 'Update script' : 'Save script' }}</button>
                @if($edit)<a class="btn btn-light" href="{{ route('dashboard.lead.scripts.index') }}">Cancel</a>@endif
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        @forelse($scripts as $s)
            <div class="page-card mb-3" style="{{ $s->is_active ? '' : 'opacity:.55' }}">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $s->name }}</div>
                        <small class="text-muted">{{ str_replace('_', ' ', $s->purpose) }} · {{ ['hi' => 'Hindi', 'hinglish' => 'Hinglish', 'en' => 'English'][$s->language] }}</small>
                    </div>
                    <div class="text-nowrap">
                        <a href="{{ route('dashboard.lead.scripts.index', ['edit' => $s->id]) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('dashboard.lead.scripts.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this script?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <p class="mt-2 mb-0 small" style="white-space:pre-wrap;color:#475569">{{ $s->body }}</p>
            </div>
        @empty
            <div class="page-card text-center text-muted py-5">No scripts yet. Write one, or add the samples.</div>
        @endforelse
    </div>
</div>
@endsection
