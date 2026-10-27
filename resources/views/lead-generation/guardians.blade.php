@extends('layouts.dashboard')
@section('title', 'Parent Contacts')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Parent Contacts</li>
@endsection

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e293b">Parent Contacts</h5>
    <p class="text-muted mb-0" style="font-size:.875rem">The agent only talks to phone numbers saved here. One number can be linked to several children — the agent then asks which child.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="page-card mb-4">
            <h6 class="fw-bold mb-3">{{ $edit ? 'Edit contact' : 'Add parent phone' }}</h6>
            <form method="POST" action="{{ $edit ? route('dashboard.lead.guardians.update', $edit) : route('dashboard.lead.guardians.store') }}">
                @csrf @if($edit) @method('PUT') @endif
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">Select…</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" @selected(old('student_id', $edit->student_id ?? '') == $s->id)>
                                {{ $s->user->name ?? '?' }} — {{ $s->class->name ?? '' }} {{ $s->section->name ?? '' }} ({{ $s->admission_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Parent name</label>
                    <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $edit->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Relation</label>
                        <select name="relation" class="form-select">
                            @foreach(['Father', 'Mother', 'Guardian', 'Parent'] as $r)
                                <option @selected(old('relation', $edit->relation ?? 'Parent') === $r)>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Language</label>
                        <select name="preferred_language" class="form-select">
                            @foreach(['hinglish' => 'Hinglish', 'hi' => 'Hindi', 'en' => 'English'] as $k => $v)
                                <option value="{{ $k }}" @selected(old('preferred_language', $edit->preferred_language ?? 'hinglish') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone number</label>
                    <input name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $edit->phone ?? '') }}" placeholder="98765 43210" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @if($edit)
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked($edit->is_active)>
                        <label class="form-check-label" for="act">Active</label>
                    </div>
                @endif
                <button class="btn btn-primary w-100">{{ $edit ? 'Update' : 'Save contact' }}</button>
                @if($edit)<a href="{{ route('dashboard.lead.guardians.index') }}" class="btn btn-light w-100 mt-2">Cancel</a>@endif
            </form>
        </div>

        <div class="page-card">
            <h6 class="fw-bold mb-2">Import from CSV</h6>
            <p class="small text-muted mb-2">Columns: <code>admission_number, parent_name, relation, phone, language</code> (language: hi, hinglish or en).</p>
            <form method="POST" action="{{ route('dashboard.lead.guardians.import') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".csv,.txt" class="form-control mb-2" required>
                <button class="btn btn-outline-primary btn-sm">Import</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="page-card">
            <form class="mb-3 d-flex gap-2" method="GET">
                <input name="search" class="form-control" placeholder="Search parent, student or phone" value="{{ request('search') }}">
                <button class="btn btn-outline-secondary">Search</button>
            </form>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Parent</th><th>Phone</th><th>Child</th><th>Lang</th><th></th></tr></thead>
                    <tbody>
                    @forelse($guardians as $g)
                        <tr style="{{ $g->is_active ? '' : 'opacity:.5' }}">
                            <td>{{ $g->name }}<br><small class="text-muted">{{ $g->relation }}</small></td>
                            <td>{{ $g->phone }}
                                @if(isset($multi[$g->phone]))<br><small class="text-primary"><i class="fas fa-children"></i> {{ $multi[$g->phone] }} children</small>@endif
                            </td>
                            <td>{{ $g->student->user->name ?? '?' }}<br><small class="text-muted">{{ $g->student->class->name ?? '' }} {{ $g->student->section->name ?? '' }}</small></td>
                            <td><small>{{ ['hi' => 'Hindi', 'hinglish' => 'Hinglish', 'en' => 'English'][$g->preferred_language] ?? '' }}</small></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('dashboard.lead.guardians.index', ['edit' => $g->id]) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('dashboard.lead.guardians.destroy', $g) }}" class="d-inline" onsubmit="return confirm('Remove this contact?')">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No parent phones saved yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $guardians->links() }}
        </div>
    </div>
</div>
@endsection
