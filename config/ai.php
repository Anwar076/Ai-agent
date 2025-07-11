<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Agent Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the AI Agent integration with local models
    |
    */

    // Ollama Configuration
    'ollama_url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'model' => env('AI_MODEL', 'llama2'),

    // Agent Personality
    'agent_name' => env('AI_AGENT_NAME', 'Anwar'),
    'company_name' => env('COMPANY_NAME', 'Brancom'),

    // Response Settings
    'max_tokens' => env('AI_MAX_TOKENS', 500),
    'temperature' => env('AI_TEMPERATURE', 0.7),
    'top_p' => env('AI_TOP_P', 0.9),

    // Fallback Settings
    'enable_fallback' => env('AI_ENABLE_FALLBACK', true),
    'max_retries' => env('AI_MAX_RETRIES', 3),
    'timeout' => env('AI_TIMEOUT', 30),
];