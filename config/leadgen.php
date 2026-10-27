<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lead Generation / AI Agent
    |--------------------------------------------------------------------------
    | Secrets live in .env, never in the database. Non-secret behaviour
    | (agent name, school knowledge, call script) is edited from
    | Dashboard → Lead Generation → AI Settings.
    */

    'default_country_code' => env('LEADGEN_DEFAULT_COUNTRY_CODE', '91'),

    // How many past messages are sent to the AI as conversation memory.
    'history_limit' => 20,

    'ai' => [
        'api_key'    => env('ANTHROPIC_API_KEY'),
        'model'      => env('ANTHROPIC_MODEL', 'claude-sonnet-5-5'),
        'max_tokens' => 700,
        'version'    => '2023-06-01',
    ],

    'whatsapp' => [
        'token'           => env('WHATSAPP_TOKEN'),            // permanent access token
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token'    => env('WHATSAPP_VERIFY_TOKEN'),     // any string you choose
        'app_secret'      => env('WHATSAPP_APP_SECRET'),       // used to verify webhook signatures
        'api_version'     => env('WHATSAPP_API_VERSION', 'v21.0'),
        // Approved template used to START a conversation (outside the 24h window).
        // Body must have exactly 2 variables: {{1}} = lead name, {{2}} = school name.
        'template_name'   => env('WHATSAPP_TEMPLATE_NAME'),
        'template_lang'   => env('WHATSAPP_TEMPLATE_LANG', 'en'),
    ],

    'voice' => [
        // Managed voice-agent provider (Vapi). Create the assistant once in their dashboard.
        'provider'        => 'vapi',
        'api_key'         => env('VAPI_API_KEY'),
        'assistant_id'    => env('VAPI_ASSISTANT_ID'),
        'phone_number_id' => env('VAPI_PHONE_NUMBER_ID'),
        'webhook_secret'  => env('VAPI_WEBHOOK_SECRET'),
        'base_url'        => env('VAPI_BASE_URL', 'https://api.vapi.ai'),
    ],
];
