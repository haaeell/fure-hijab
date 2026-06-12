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

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds' => env('MIDTRANS_IS_3DS', true),
    ],

    'biteship' => [
        'api_key' => env('BITESHIP_API_KEY'),
        'webhook_secret' => env('BITESHIP_WEBHOOK_SECRET'),
        'origin_area_id' => env('BITESHIP_ORIGIN_AREA_ID'),
        'origin_contact_name' => env('BITESHIP_ORIGIN_CONTACT_NAME', env('APP_NAME', 'FURE')),
        'origin_contact_phone' => env('BITESHIP_ORIGIN_CONTACT_PHONE'),
        'origin_address' => env('BITESHIP_ORIGIN_ADDRESS'),
        'origin_postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE'),
        'origin_latitude' => env('BITESHIP_ORIGIN_LATITUDE'),
        'origin_longitude' => env('BITESHIP_ORIGIN_LONGITUDE'),
    ],
];
