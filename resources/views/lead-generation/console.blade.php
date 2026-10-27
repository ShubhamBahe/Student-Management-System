@extends('layouts.dashboard')
@section('title', 'Voice Console')

@section('breadcrumb')
    <li class="breadcrumb-item">Lead Generation</li>
    <li class="breadcrumb-item active">Voice Console</li>
@endsection

@push('styles')
<style>
    .chat-box { height: 440px; overflow-y: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; }
    .bubble { max-width: 82%; padding: 10px 14px; border-radius: 14px; margin-bottom: 10px; white-space: pre-wrap; font-size: .9rem; line-height: 1.45; }
    .bubble.parent { background: #2563eb; color: #fff; margin-left: auto; border-bottom-right-radius: 4px; }
    .bubble.agent { background: #fff; color: #1e293b; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; }
    .bubble.agent.owner { border-color: #c4b5fd; background: #f5f3ff; }
    .bubble .meta { display: block; font-size: .7rem; opacity: .65; margin-top: 6px; }
    .chip { border: 1px solid #cbd5e1; background: #fff; border-radius: 999px; padding: 4px 12px; font-size: .78rem; cursor: pointer; }
    .chip:hover { background: #eff6ff; border-color: #2563eb; }
    .mic-on { background: #dc2626 !important; border-color: #dc2626 !important; color: #fff !important; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1" style="color:#1e293b">Voice Console</h5>
        <p class="text-muted mb-0" style="font-size:.875rem">Test the agent exactly as a parent would meet it. Speak with the microphone or type.
            Mode: <strong>{{ $aiActive ? 'AI (Claude)' : 'Rule-based' }}</strong></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="page-card mb-4">
            <label class="form-label fw-semibold">Caller phone number</label>
            <select id="guardianSelect" class="form-select mb-2">
                <option value="">— choose a saved parent —</option>
                @foreach($guardians as $g)
                    <option value="{{ $g->phone }}">{{ $g->phone }} · {{ $g->name }} ({{ $g->student->user->name ?? '?' }})</option>
                @endforeach
            </select>
            <input id="phone" class="form-control" placeholder="…or type any number (try an unknown one)" value="{{ $guardians->first()->phone ?? '' }}">
            <small class="text-muted d-block mt-1">An unknown number gets blocked for the configured time after repeated failed attempts.</small>

            <hr>
            <label class="form-label fw-semibold">Quick questions</label>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach([
                    'Attendance kitni hai?', 'Test marks batao', 'Weakest and strongest subject?', 'Fees kitni baaki hai?',
                    'Teacher remarks kya hain?', 'Poori report do', 'मेरे बच्चे की उपस्थिति बताइए', 'How is my child doing?',
                    'I want a refund', 'Meri shikayat hai', 'I will take legal action', 'Student ID 99999'
                ] as $q)
                    <button type="button" class="chip" data-q="{{ $q }}">{{ $q }}</button>
                @endforeach
            </div>

            <label class="form-label fw-semibold">Simulate an attachment</label>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="sendVoiceNote"><i class="fas fa-microphone me-1"></i>Voice note</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="sendImage"><i class="fas fa-image me-1"></i>Image</button>
            </div>
        </div>

        <div class="page-card">
            <label class="form-label fw-semibold">Rehearse an outbound call script</label>
            <select id="scriptSelect" class="form-select mb-2">
                <option value="">— call script —</option>
                @foreach($scripts as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
            </select>
            <select id="studentSelect" class="form-select mb-2">
                <option value="">— student —</option>
                @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->user->name ?? '?' }}</option>@endforeach
            </select>
            <button class="btn btn-outline-primary btn-sm" id="playScript"><i class="fas fa-play me-1"></i>Preview &amp; speak</button>
            <div id="scriptText" class="small mt-2 text-muted"></div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="page-card">
            <div id="chat" class="chat-box mb-3">
                <div class="text-center text-muted small mt-5" id="emptyNote">Say hello, or tap a quick question.</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button class="btn btn-outline-secondary" id="micBtn" title="Speak"><i class="fas fa-microphone"></i></button>
                <select id="speechLang" class="form-select" style="width:auto">
                    <option value="hi-IN">Hindi / Hinglish</option>
                    <option value="en-IN">English</option>
                </select>
                <input id="msg" class="form-control" placeholder="Type or speak the parent's question…" autocomplete="off">
                <button class="btn btn-primary" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" id="speakToggle" checked>
                <label class="form-check-label small" for="speakToggle">Speak the agent's replies aloud</label>
            </div>
            <small class="text-muted">Voice input and speech output use your browser. Chrome or Edge work best.</small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const chat = document.getElementById('chat');
    const phone = document.getElementById('phone');
    const msg = document.getElementById('msg');
    const urls = {!! json_encode(['send' => route('dashboard.lead.console.send'), 'preview' => route('dashboard.lead.console.preview')]) !!};

    document.getElementById('guardianSelect').addEventListener('change', e => { if (e.target.value) phone.value = e.target.value; });

    function add(kind, text, meta, owner) {
        const empty = document.getElementById('emptyNote'); if (empty) empty.remove();
        const d = document.createElement('div');
        d.className = 'bubble ' + kind + (owner ? ' owner' : '');
        d.textContent = text;
        if (meta) { const m = document.createElement('span'); m.className = 'meta'; m.textContent = meta; d.appendChild(m); }
        chat.appendChild(d); chat.scrollTop = chat.scrollHeight;
    }

    function speak(text, lang) {
        if (!document.getElementById('speakToggle').checked || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text.replace(/^\s*\d+\.\s+/gm, '').replace(/\n+/g, '. '));
        u.lang = lang === 'English' ? 'en-IN' : 'hi-IN';
        window.speechSynthesis.speak(u);
    }

    async function send(text, media) {
        if (!phone.value.trim()) { alert('Choose or type a phone number first.'); return; }
        add('parent', media ? (media === 'voice' ? '🎤 (voice note)' : '🖼️ (image)') : text);
        try {
            const r = await fetch(urls.send, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ phone: phone.value, message: text || '', media: media || null, channel: 'web' })
            });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const j = await r.json();
            add('agent', j.reply, j.language + ' · ' + j.status + (j.topics.length ? ' · ' + j.topics.join(', ') : ''), j.status === 'escalated');
            speak(j.reply, j.language);
        } catch (e) {
            add('agent', 'Could not reach the server: ' + e.message);
        }
    }

    function submit() { const t = msg.value.trim(); if (!t) return; msg.value = ''; send(t); }
    document.getElementById('sendBtn').addEventListener('click', submit);
    msg.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); submit(); } });
    document.querySelectorAll('.chip').forEach(c => c.addEventListener('click', () => send(c.dataset.q)));
    document.getElementById('sendVoiceNote').addEventListener('click', () => send('', 'voice'));
    document.getElementById('sendImage').addEventListener('click', () => send('', 'image'));

    // microphone (browser speech recognition)
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    const mic = document.getElementById('micBtn');
    if (!SR) { mic.disabled = true; mic.title = 'Speech recognition is not supported in this browser'; }
    else {
        const rec = new SR(); rec.interimResults = false; rec.maxAlternatives = 1;
        let on = false;
        rec.onresult = e => { const t = e.results[0][0].transcript; msg.value = t; submit(); };
        rec.onend = () => { on = false; mic.classList.remove('mic-on'); };
        rec.onerror = () => { on = false; mic.classList.remove('mic-on'); };
        mic.addEventListener('click', () => {
            if (on) { rec.stop(); return; }
            window.speechSynthesis && window.speechSynthesis.cancel();
            rec.lang = document.getElementById('speechLang').value;
            try { rec.start(); on = true; mic.classList.add('mic-on'); } catch (e) {}
        });
    }

    // rehearse a call script
    document.getElementById('playScript').addEventListener('click', async () => {
        const s = document.getElementById('scriptSelect').value, st = document.getElementById('studentSelect').value;
        if (!s || !st) { alert('Choose a script and a student.'); return; }
        const r = await fetch(urls.preview + '?script_id=' + s + '&student_id=' + st, { headers: { 'Accept': 'application/json' } });
        if (!r.ok) { alert('Could not render the script.'); return; }
        const j = await r.json();
        document.getElementById('scriptText').textContent = j.text;
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(j.text); u.lang = j.language === 'en' ? 'en-IN' : 'hi-IN';
            window.speechSynthesis.speak(u);
        }
    });
})();
</script>
@endpush
