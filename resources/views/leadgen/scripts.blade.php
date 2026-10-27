@extends('layouts.dashboard')
@section('title', 'Scripts')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item active">Scripts</li>
@endsection

@section('content')
<div class="mb-4">
    <h5 class="fw-bold mb-1" style="color:#1e293b">Scripts</h5>
    <p class="text-muted mb-0" style="font-size:0.875rem">
        What the agent says. Placeholders: <code>{name}</code> <code>{student_name}</code> <code>{class}</code> <code>{school}</code>.
        Call scripts guide the AI voice agent (it still answers questions freely); WhatsApp scripts are the opening message.
    </p>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
    <div class="col-lg-5">
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">New script</h6>
            <form method="POST" action="{{ route('dashboard.leadgen.scripts.store') }}">
                @csrf
                <div class="mb-2"><input name="name" class="form-control" placeholder="Name, e.g. Admission follow-up" required></div>
                <div class="mb-2">
                    <select name="channel" class="form-select">
                        <option value="call">Phone call</option>
                        <option value="whatsapp">WhatsApp opening message</option>
                    </select>
                </div>
                <div class="mb-2"><textarea name="content" rows="7" class="form-control" required
placeholder="Hello {name}, this is the admission team of {school}. You enquired about admission for {student_name} in {class}. ..."></textarea></div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="def">
                    <label class="form-check-label" for="def">Use as default for this channel</label>
                </div>
                <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save script</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        @forelse($scripts as $s)
            <div class="page-card mb-3">
                <form method="POST" action="{{ route('dashboard.leadgen.scripts.update', $s) }}">
                    @csrf @method('PUT')
                    <div class="d-flex gap-2 mb-2">
                        <input name="name" class="form-control form-control-sm fw-semibold" value="{{ $s->name }}" required>
                        <select name="channel" class="form-select form-select-sm" style="width:auto">
                            <option value="call" @selected($s->channel === 'call')>Call</option>
                            <option value="whatsapp" @selected($s->channel === 'whatsapp')>WhatsApp</option>
                        </select>
                    </div>
                    <textarea name="content" rows="5" class="form-control form-control-sm mb-2" required>{{ $s->content }}</textarea>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="def{{ $s->id }}" @checked($s->is_default)>
                            <label class="form-check-label small" for="def{{ $s->id }}">Default</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary">Update</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('del-s{{ $s->id }}').submit()">Delete</button>
                        </div>
                    </div>
                </form>
                <form method="POST" action="{{ route('dashboard.leadgen.scripts.destroy', $s) }}" id="del-s{{ $s->id }}" class="d-none">@csrf @method('DELETE')</form>
            </div>
        @empty
            <div class="page-card text-center text-muted py-5">No scripts yet. Without one, the agent uses a short built-in introduction.</div>
        @endforelse
    </div>
</div>
@endsection
