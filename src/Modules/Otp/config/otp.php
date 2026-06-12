<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | OTP Default Configuration
    |--------------------------------------------------------------------------
    |
    | Default values for One-Time Password generation and validation.
    |
    */
    'default_expiry_minutes' => env('OTP_DEFAULT_EXPIRY_MINUTES', 10),
    'default_max_attempts' => env('OTP_DEFAULT_MAX_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the languages supported for OTP messages and error handling.
    |
    */
    'localization' => [
        'locale' => env('OTP_LOCALE', 'en'),
        'supported_locales' => env('OTP_SUPPORTED_LOCALES', 'fr,en')
            ? explode(',', env('OTP_SUPPORTED_LOCALES', 'fr,en'))
            : ['fr', 'en'],
        'fallback_locale' => env('OTP_FALLBACK_LOCALE', 'en'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Rate limiting settings to prevent brute force attacks.
    |
    */
    'security' => [
        'rate_limit_requests' => env('OTP_RATE_LIMIT_REQUESTS', 3),
        'rate_limit_verifications' => env('OTP_RATE_LIMIT_VERIFICATIONS', 5),
        'rate_limit_decay_minutes' => env('OTP_RATE_LIMIT_DECAY_MINUTES', 60),
        'failed_verification_decay_seconds' => env('OTP_FAILED_VERIFICATION_DECAY_SECONDS', 300),
        'rate_limit_hit_decay_seconds' => env('OTP_RATE_LIMIT_HIT_DECAY_SECONDS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Recovery Codes Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for backup codes generation.
    |
    */
    'recovery_codes' => [
        'characters' => env('RECOVERY_CODES_CHARACTERS', 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'),
        'default_count' => env('RECOVERY_CODES_DEFAULT_COUNT', 8),
        'default_length' => env('RECOVERY_CODES_DEFAULT_LENGTH', 10),
        'hash_algorithm' => env('RECOVERY_CODES_HASH_ALGORITHM', 'sha256'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Automatic cleanup of expired OTPs.
    |
    */
    'cleanup' => [
        'auto_cleanup' => env('OTP_AUTO_CLEANUP', true),
        'frequency' => env('OTP_CLEANUP_FREQUENCY', 60),
        'retention_days' => env('OTP_RETENTION_DAYS', 30),
    ],
];
