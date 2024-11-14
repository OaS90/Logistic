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
        'token' => env('DADATA_TOKEN', ''),
        'secret' => env('DADATA_SECRET', '')
    ],
    'delivery_holodilnik_service' => [
        'uri' => env('DELIVERY_HOLODILNIK_SERVICE', 'http://gateway-stage.adeal.ru/v1.0/holodilnik-delivery/api/v1/'),
        'login' => env('DELIVERY_HOLODILNIK_LOGIN', 'test@test.ru'),
        'password' => env('DELIVERY_HOLODILNIK_PASSWORD', '123'),
    ],
    'config_service' => [
        'uri' => env('CONFIG_SERVICE_URI', ''),
        'token' => env('CONFIG_SERVICE_TOKEN', ''),
    ]
];
