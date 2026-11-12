<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI (draft catatan evaluasi rapor)
    |--------------------------------------------------------------------------
    |
    | Satu konfigurasi saja — atur base URL, model, dan API key sendiri. `driver`
    | menentukan format request yang dipakai:
    |   - openai    : POST {base_url}/chat/completions (format paling umum:
    |                 [OI], OpenRouter, Groq, DeepSeek, Mistral, Ollama, LM Studio,
    |                 vLLM, dan proxy lain yang kompatibel)
    |   - gemini    : POST {base_url}/models/{model}:generateContent
    |   - anthropic : POST {base_url}/messages
    |
    | `label` hanya untuk tooltip di UI; kalau kosong dipakai nama default driver.
    | Set AI_WAJIB_KUNCI=false untuk endpoint lokal tanpa API key (Ollama/LM Studio).
    |
    */

    'ai' => [
        'driver' => env('AI_DRIVER', 'openai'),
        'label' => env('AI_LABEL'),
        'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'),
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL'),
        'timeout' => (int) env('AI_TIMEOUT', 60),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 4096),
        'wajib_kunci' => (bool) env('AI_WAJIB_KUNCI', true),
    ],

];
