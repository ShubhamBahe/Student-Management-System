@extends('layouts.dashboard')
@section('title', 'Test AI Agent')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item active">Test AI Agent</li>
@endsection

@push('styles')
<style>
    #sbChat { height: 440px; overflow-y: auto; background: #efeae2; border-radius: 12px; padding: 16px; }
    .sb { max-width: 78%; padding: 8px 12px; border-radius: 10px; margin-bottom: 8px; font-size: 0.875rem; white-space: pre-wrap; box-shadow: 0 1px 1px rgba(0,0,0,.08); }
    .sb.user { background: #d9fdd3; margin-left: auto; }
    .sb.assistant { background: #fff; margin-right: auto; }
    .sb.error { background: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="page-card">
            <h6 class="fw-bold mb-1" style="color:#1e293b">Test AI Agent</h6>
            <p class="text-muted small">Chat with the agent exactly as a parent would on WhatsApp. Nothing is sent to anyone and no lead is saved.</p>

            @unless($configured)
                <div class="alert alert-warning">Set <code>ANTHROPIC_API_KEY</code> in <code>.env</code> to enable the AI.</div>
            @endunless

            <div id="sbChat" class="d-flex flex-column"></div>

            <form id="sbForm" class="mt-3 d-flex gap-2">
                <input id="sbInput" class="form-control" placeholder="Type a message, e.g. What is the admission process for Class 5?" autocomplete="off" required>
                <button class="btn btn-success" id="sbBtn"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b">Pretend to be…</h6>
            <label class="form-label small">Phone number</label>
            <input id="sbPhone" class="form-control mb-2" placeholder="Leave empty = a stranger / new lead">
            <div class="form-text mb-3">Enter a parent's or student's registered number to test attendance, marks and fee answers. Unregistered numbers must be refused private data.</div>
            <div id="sbVerified" class="small"></div>
            <hr>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetChat()"><i class="fas fa-redo me-1"></i>New conversation</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let history = [];
    const chat = document.getElementById('sbChat');

    function add(role, text) {
        const d = document.createElement('div');
        d.className = 'sb ' + role; d.textContent = text;
        chat.appendChild(d); chat.scrollTop = chat.scrollHeight;
        return d;
    }
    function resetChat() { history = []; chat.innerHTML = ''; document.getElementById('sbVerified').innerHTML = ''; }

    document.getElementById('sbForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('sbInput');
        const text = input.value.trim(); if (!text) return;
        input.value = ''; add('user', text); history.push({role: 'user', content: text});
        const btn = document.getElementById('sbBtn'); btn.disabled = true;
        const typing = add('assistant', '…');
        try {
            const res = await fetch('{{ route('dashboard.leadgen.sandbox.send') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json',
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({messages: history, phone: document.getElementById('sbPhone').value})
            });
            const data = await res.json();
            typing.remove();
            if (!res.ok) { add('error', data.error || data.message || 'Something went wrong'); history.pop(); }
            else {
                add('assistant', data.reply); history.push({role: 'assistant', content: data.reply});
                const v = document.getElementById('sbVerified');
                v.innerHTML = data.verified.length
                    ? '<span class="text-success"><i class="fas fa-check-circle"></i> Verified: ' + data.verified.join(', ') + '</span>'
                    : (document.getElementById('sbPhone').value ? '<span class="text-danger"><i class="fas fa-times-circle"></i> Number not registered with any student</span>' : '');
            }
        } catch (err) { typing.remove(); add('error', 'Network error'); history.pop(); }
        btn.disabled = false; input.focus();
    });
</script>
@endpush
