<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('OPENAI_API_KEY'),
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    */

    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4'),

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    */

    'models' => [
        'gpt-4' => [
            'context_window' => 8192,
            'pricing' => ['input' => 0.03 / 1000, 'output' => 0.06 / 1000],
        ],
        'gpt-4-turbo' => [
            'context_window' => 128000,
            'pricing' => ['input' => 0.01 / 1000, 'output' => 0.03 / 1000],
        ],
        'gpt-3.5-turbo' => [
            'context_window' => 4096,
            'pricing' => ['input' => 0.0005 / 1000, 'output' => 0.0015 / 1000],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'enabled' => env('OPENAI_RATE_LIMITING_ENABLED', true),
        'max_requests_per_hour' => env('OPENAI_MAX_REQUESTS_PER_HOUR', 100),
        'block_when_exceeded' => env('OPENAI_BLOCK_ON_RATE_LIMIT', false),
        'persistent_storage' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */

    'caching' => [
        'enabled' => env('OPENAI_CACHE_ENABLED', true),
        'default_ttl' => env('OPENAI_CACHE_TTL', 3600),
        'max_cache_size' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Optimization
    |--------------------------------------------------------------------------
    */

    'context_optimization' => [
        'enabled' => true,
        'reserved_percentage' => 0.15,
        'truncate_strategy' => 'oldest',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('OPENAI_LOG_REQUESTS', false),
        'log_all_requests' => false,
        'log_cost' => true,
        'channel' => 'stack',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'table_prefix' => 'openai_',
    ],

    /*
    |--------------------------------------------------------------------------
    | RBAC Roles & Permissions (spatie/laravel-permission guard)
    |--------------------------------------------------------------------------
    */

    'rbac' => [
        'guard' => 'web',
        'roles' => ['admin', 'member', 'viewer'],
        'default_role' => 'member',
        'permissions' => [
            'create_conversation',
            'manage_conversations',
            'delete_conversation',
            'manage_templates',
            'manage_api_keys',
            'manage_budget',
            'manage_team',
            'view_usage',
        ],
        'role_permissions' => [
            'admin' => [
                'create_conversation', 'manage_conversations', 'delete_conversation',
                'manage_templates', 'manage_api_keys', 'manage_budget', 'manage_team', 'view_usage',
            ],
            'member' => [
                'create_conversation', 'manage_conversations', 'manage_templates', 'view_usage',
            ],
            'viewer' => [
                'view_usage',
            ],
        ],
    ],
];
