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
    
    'firebase' => [
        'api_key' => env('FCM_API_KEY'),
        'auth_domain' => env('FCM_AUTH_DOMAIN'),
        'project_id' => env('FCM_PROJECT_ID'),
        'storage_bucket' => env('FCM_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FCM_MESSAGING_SENDER_ID'),
        'app_id' => env('FCM_APP_ID'),
        'measurement_id' => env('FCM_MEASUREMENT_ID'),
    ],

];
