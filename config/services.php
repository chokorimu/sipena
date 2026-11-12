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
    | Penyedia AI (evaluasi naratif rapor)
    |--------------------------------------------------------------------------
    |
    | Pilih penyedia aktif lewat AI_PROVIDER. Setiap entri punya `driver` yang
    | menentukan format API-nya:
    |   - gemini    : Google Gemini generateContent
    |   - openai    : chat completions ala [OI] (dipakai juga oleh OpenRouter,
    |                 Groq, DeepSeek, Mistral, xAI, dan Ollama lokal)
    |   - anthropic : Claude Messages API
    |
    | Menambah penyedia baru yang kompatibel chat completions cukup menambah
    | entri di bawah dengan `driver` => 'openai'.
    |
    */

    'ai' => [
        'default' => env('AI_PROVIDER', 'gemini'),
        'timeout' => (int) env('AI_TIMEOUT', 60),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 4096),

        'providers' => [
            'gemini' => [
                'driver' => 'gemini',
                'label' => 'Gemini',
                'api_key' => env('GEMINI_API_KEY'),
                'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
            ],

            'openai' => [
                'driver' => 'openai',
                'label' => '[OI]',
                'api_key' => env('OPENAI_API_KEY'),
                'env_key' => 'OPENAI_API_KEY',
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            ],

            'openrouter' => [
                'driver' => 'openai',
                'label' => 'OpenRouter',
                'api_key' => env('OPENROUTER_API_KEY'),
                'env_key' => 'OPENROUTER_API_KEY',
                'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.3-70b-instruct:free'),
                'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
                'headers' => [
                    'HTTP-Referer' => env('APP_URL', 'http://localhost'),
                    'X-Title' => env('APP_NAME', 'SIPENA'),
                ],
            ],

            'groq' => [
                'driver' => 'openai',
                'label' => 'Groq',
                'api_key' => env('GROQ_API_KEY'),
                'env_key' => 'GROQ_API_KEY',
                'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
            ],

            'deepseek' => [
                'driver' => 'openai',
                'label' => 'DeepSeek',
                'api_key' => env('DEEPSEEK_API_KEY'),
                'env_key' => 'DEEPSEEK_API_KEY',
                'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
                'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
            ],

            'mistral' => [
                'driver' => 'openai',
                'label' => 'Mistral',
                'api_key' => env('MISTRAL_API_KEY'),
                'env_key' => 'MISTRAL_API_KEY',
                'model' => env('MISTRAL_MODEL', 'mistral-small-latest'),
                'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
            ],

            'ollama' => [
                'driver' => 'openai',
                'label' => 'Ollama',
                'api_key' => null,
                'env_key' => 'OLLAMA_BASE_URL',
                'model' => env('OLLAMA_MODEL', 'llama3.2'),
                'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434/v1'),
                // Ollama lokal tidak butuh API key.
                'wajib_kunci' => false,
            ],

            'anthropic' => [
                'driver' => 'anthropic',
                'label' => 'Claude',
                'api_key' => env('ANTHROPIC_API_KEY'),
                'env_key' => 'ANTHROPIC_API_KEY',
                'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
                'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
            ],
        ],
    ],

];
