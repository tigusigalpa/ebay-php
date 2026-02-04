<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The eBay API environment to use. Options: 'sandbox' or 'production'
    |
    */
    'environment' => env('EBAY_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Credentials
    |--------------------------------------------------------------------------
    |
    | Your eBay sandbox API credentials
    |
    */
    'sandbox' => [
        'app_id' => env('EBAY_SANDBOX_APP_ID', ''),
        'cert_id' => env('EBAY_SANDBOX_CERT_ID', ''),
        'dev_id' => env('EBAY_SANDBOX_DEV_ID', ''),
        'runame' => env('EBAY_SANDBOX_RUNAME', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Production Credentials
    |--------------------------------------------------------------------------
    |
    | Your eBay production API credentials
    |
    */
    'production' => [
        'app_id' => env('EBAY_PRODUCTION_APP_ID', ''),
        'cert_id' => env('EBAY_PRODUCTION_CERT_ID', ''),
        'dev_id' => env('EBAY_PRODUCTION_DEV_ID', ''),
        'runame' => env('EBAY_PRODUCTION_RUNAME', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The eBay API compatibility level
    | @link https://developer.ebay.com/DevZone/XML/docs/ReleaseNotes.html
    |
    */
    'compatibility_level' => 1257,

    /*
    |--------------------------------------------------------------------------
    | Default Site
    |--------------------------------------------------------------------------
    |
    | Default eBay site/marketplace to use
    |
    */
    'default_site' => env('EBAY_DEFAULT_SITE', 'US'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes
    |--------------------------------------------------------------------------
    |
    | Default OAuth scopes for user authorization
    |
    */
    'scopes' => [
        'https://api.ebay.com/oauth/api_scope',
        'https://api.ebay.com/oauth/api_scope/sell.marketing.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.marketing',
        'https://api.ebay.com/oauth/api_scope/sell.inventory.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.inventory',
        'https://api.ebay.com/oauth/api_scope/sell.account.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.account',
        'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
        'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
        'https://api.ebay.com/oauth/api_scope/sell.finances',
        'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
        'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',
        'https://api.ebay.com/oauth/api_scope/commerce.notification.subscription',
        'https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Storage
    |--------------------------------------------------------------------------
    |
    | Configure how OAuth tokens are stored. Options: 'database', 'cache', 'file'
    |
    */
    'token_storage' => env('EBAY_TOKEN_STORAGE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for API responses and tokens
    |
    */
    'cache' => [
        'enabled' => env('EBAY_CACHE_ENABLED', true),
        'ttl' => env('EBAY_CACHE_TTL', 3600),
        'prefix' => 'ebay_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable/disable API request and response logging
    |
    */
    'logging' => [
        'enabled' => env('EBAY_LOGGING_ENABLED', false),
        'channel' => env('EBAY_LOG_CHANNEL', 'stack'),
    ],
];
