<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Translation Driver
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "google", "deepl", "manual"
    |
    */
    'driver' => env('AUTO_TRANSLATION_DRIVER', 'google'),

    /*
    |--------------------------------------------------------------------------
    | Google Translate API Configuration
    |--------------------------------------------------------------------------
    |
    | Get your API key from: https://console.cloud.google.com/apis/credentials
    | Documentation: https://cloud.google.com/translate/docs
    |
    */
    'google' => [
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY', ''),
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Source Language
    |--------------------------------------------------------------------------
    |
    | The default source language for translations (usually English)
    |
    */
    'source_locale' => env('AUTO_TRANSLATION_SOURCE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Batch Size
    |--------------------------------------------------------------------------
    |
    | Number of translations to send per API request (max 128 for Google)
    |
    */
    'batch_size' => 100,

    /*
    |--------------------------------------------------------------------------
    | Preserve Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns that should not be translated (placeholders, variables, etc.)
    |
    */
    'preserve_patterns' => [
        '/:[\w]+/',           // Laravel placeholders like :attribute
        '/\{[\w]+\}/',        // Curly brace variables like {name}
        '/\[[\w]+\]/',        // Square bracket variables like [app_name]
        '/@[\w]+/',           // @ mentions
        '/#[\w]+/',           // # hashtags
    ],

    /*
    |--------------------------------------------------------------------------
    | Skip Keys
    |--------------------------------------------------------------------------
    |
    | Translation keys that should be skipped (not translated)
    |
    */
    'skip_keys' => [
        'validation.attributes',
        'validation.custom',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Delay between API requests in milliseconds to avoid rate limiting
    |
    */
    'rate_limit_delay' => 100, // 100ms between requests

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts and delay for failed translations
    |
    */
    'retry' => [
        'attempts' => 3,
        'delay' => 1000, // 1 second
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Translations
    |--------------------------------------------------------------------------
    |
    | Enable logging of translation activities
    |
    */
    'log_enabled' => env('AUTO_TRANSLATION_LOG', true),
];
