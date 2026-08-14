<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Assistant Enabled
    |--------------------------------------------------------------------------
    |
    | Toggle the AI Assistant feature on/off globally.
    |
    */
    'enabled' => env('AI_ASSISTANT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | LLM Provider
    |--------------------------------------------------------------------------
    |
    | The LLM provider to use. Currently supported: 'gemini'
    |
    */
    'provider' => env('AI_ASSISTANT_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversation Settings
    |--------------------------------------------------------------------------
    |
    | max_history: Maximum number of messages to include as context
    | session_timeout: Minutes before a session is considered expired
    |
    */
    'max_history' => env('AI_ASSISTANT_MAX_HISTORY', 20),
    'session_timeout' => env('AI_ASSISTANT_SESSION_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Response Language
    |--------------------------------------------------------------------------
    |
    | Default language for AI responses: 'id' (Indonesian) or 'en' (English)
    |
    */
    'language' => env('AI_ASSISTANT_LANGUAGE', 'id'),

];
