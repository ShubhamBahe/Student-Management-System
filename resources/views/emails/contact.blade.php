<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #1e293b, #2563eb); padding: 30px; text-align: center; }
        .header h2 { color: #fff; margin: 0; font-size: 1.5rem; }
        .header p { color: rgba(255,255,255,0.8); margin: 5px 0 0; font-size: 0.875rem; }
        .body { padding: 30px; }
        .field { margin-bottom: 20px; padding: 16px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #2563eb; }
        .label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .value { font-size: 0.95rem; color: #1e293b; font-weight: 500; }
        .message-box { background: #f8fafc; border-radius: 8px; padding: 16px; border: 1px solid #e2e8f0; line-height: 1.7; color: #475569; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 0.8rem; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📩 New Contact Message</h2>
            <p>{{ config('app.name') }} — School Management System</p>
        </div>
        <div class="body">
            <div class="field">
                <div class="label">👤 Sender Name</div>
                <div class="value">{{ $data['name'] }}</div>
            </div>
            <div class="field">
                <div class="label">📧 Email Address</div>
                <div class="value">{{ $data['email'] }}</div>
            </div>
            @if(!empty($data['phone']))
            <div class="field">
                <div class="label">📞 Phone Number</div>
                <div class="value">{{ $data['phone'] }}</div>
            </div>
            @endif
            @if(!empty($data['subject']))
            <div class="field">
                <div class="label">📋 Subject</div>
                <div class="value">{{ $data['subject'] }}</div>
            </div>
            @endif
            <div class="label" style="margin-bottom:8px">💬 Message</div>
            <div class="message-box">{{ $data['message'] }}</div>
            <div style="margin-top:20px;padding:12px;background:#dbeafe;border-radius:8px;font-size:0.8rem;color:#1d4ed8">
                📅 Received on: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
        <div class="footer">
            <p>This message was sent from the contact form on your school website.</p>
        </div>
    </div>
</body>
</html>