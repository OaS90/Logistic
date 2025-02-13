<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'dadata' => [
        'suggest_url' => env('DADATA_SUGGEST_URL', ''),
        'clean_url' => env('DADATA_CLEAN_URL'),
        'token' => env('DADATA_TOKEN', ''),
        'secret' => env('DADATA_SECRET', '')
    ],
    'monolith' => [
        'token' => env('MONOLITH_TOKEN', ''),
    ],
    'delivery_service' => [
        'token' => env('DELIVERY_SERVICE_TOKEN', ''),
    ],
    'config_service' => [
        'token' => env('CONFIG_SERVICE_TOKEN', ''),
    ],
    'kraken' => [
        'base_uri' => '',
        'uri' => env('KRAKEN_URI', 'https://krakend-stage.adeal.ru/api/v1/'),
        'token' => env('KRAKEN_TOKEN', ''),
    ]
];
