<?php

return [
    'diagnostics' => [
        'max_depth' => 4,
        'max_items' => 25,
        'max_string_length' => 240,
    ],

    'headers' => [
        'content_type_options' => 'nosniff',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'frame_options' => 'SAMEORIGIN',
        'permissions_policy' => 'camera=(), microphone=(), geolocation=(), payment=()',

        'hsts' => [
            'enabled' => (bool) env('SECURITY_HSTS_ENABLED', false),
            'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
            'include_subdomains' => (bool) env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
            'preload' => (bool) env('SECURITY_HSTS_PRELOAD', false),
        ],
    ],
];
