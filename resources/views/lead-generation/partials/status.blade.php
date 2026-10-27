@php
    $map = [
        'ok' => ['#dcfce7', '#166534', 'Answered'],
        'blocked' => ['#fee2e2', '#991b1b', 'Blocked'],
        'escalated' => ['#ede9fe', '#5b21b6', 'Sent to owner'],
        'needs_text' => ['#e0f2fe', '#075985', 'Asked for text'],
        'unregistered' => ['#fef9c3', '#854d0e', 'Unknown number'],
        'clarify' => ['#e0f2fe', '#075985', 'Asked which child'],
        'error' => ['#fee2e2', '#991b1b', 'System issue'],
    ];
    [$bg, $fg, $label] = $map[$status] ?? ['#f1f5f9', '#475569', $status];
@endphp
<span class="badge" style="background:{{ $bg }};color:{{ $fg }};font-weight:600">{{ $label }}</span>
