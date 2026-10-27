<?php

/*
|--------------------------------------------------------------------------
| Lead Generation / AI Voice Agent
|--------------------------------------------------------------------------
| Secrets live in .env. Non-secret options (owner phone, block rules, call
| window...) can be changed from  Dashboard > Lead Generation > Settings
| and are stored in the `settings` table with a `lead_` prefix.
*/
return [
    // Owner who receives complaints / refund / legal / unknown-ID alerts (WhatsApp or SMS)
    'owner_name'  => env('LEAD_OWNER_NAME', 'School Owner'),
    'owner_phone' => env('LEAD_OWNER_PHONE'),

    // Security rules
    'max_failed_attempts' => (int) env('LEAD_MAX_FAILED_ATTEMPTS', 3),
    'block_minutes'       => (int) env('LEAD_BLOCK_MINUTES', 60),

    // Phone handling
    'default_country_code' => env('LEAD_COUNTRY_CODE', '91'),

    // AI (Anthropic Claude). Leave the key empty to run in built-in "rule based" mode.
    'ai' => [
        'enabled' => (bool) env('LEAD_AI_ENABLED', true),
        'key'     => env('ANTHROPIC_API_KEY'),
        'model'   => env('LEAD_AI_MODEL', 'claude-haiku-5-5'),
        'timeout' => (int) env('LEAD_AI_TIMEOUT', 20),
    ],

    // Twilio (WhatsApp + phone calls). Leave empty to use only the in-panel console.
    'twilio' => [
        'sid'           => env('TWILIO_SID'),
        'token'         => env('TWILIO_TOKEN'),
        'voice_from'    => env('TWILIO_VOICE_FROM'),       // e.g. +1415XXXXXXX (a Twilio voice number)
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),    // e.g. +14155238886
        'sms_from'      => env('TWILIO_SMS_FROM'),
        'voice'         => env('TWILIO_TTS_VOICE', 'Polly.Aditi'),
        'speech_lang'   => env('TWILIO_SPEECH_LANG', 'hi-IN'),
        'verify_signature' => (bool) env('TWILIO_VERIFY_SIGNATURE', true),
    ],

    // Outbound calls are only placed inside this window (app timezone)
    'call_window' => ['start' => env('LEAD_CALL_START', '09:00'), 'end' => env('LEAD_CALL_END', '19:00')],
];
