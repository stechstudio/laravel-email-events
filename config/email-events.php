<?php

return [
    'url' => env('MAIL_EVENTS_URL', '.hooks/email-events'),

    'token' => env('MAIL_EVENTS_AUTH_TOKEN'),
    'token_parameter' => env('MAIL_EVENTS_AUTH_TOKEN_PARAM', 'auth'),

    'basic_username' => env('MAIL_EVENTS_AUTH_USERNAME'),
    'basic_password' => env('MAIL_EVENTS_AUTH_PASSWORD'),

    'signature_key' => env('MAIL_EVENTS_SIGNATURE_KEY', env('MAILGUN_SECRET')),

    'authorizers' => [
        'token' => \STS\EmailEvents\Auth\TokenAuth::class,
        'basic' => \STS\EmailEvents\Auth\BasicHttpAuth::class,
        'signature' => \STS\EmailEvents\Auth\SignatureAuth::class,
        'user-agent' => \STS\EmailEvents\Auth\UserAgentAuth::class
    ],

    'providers' => [
        'sendgrid' => [
            'adapter' => \STS\EmailEvents\Adapters\SendGrid::class,
            'auth' => env('MAIL_EVENTS_SENDGRID_AUTH', 'token')
        ],
        'postmark' => [
            'adapter' => \STS\EmailEvents\Adapters\Postmark::class,
            'auth' => env('MAIL_EVENTS_POSTMARK_AUTH', 'token')
        ],
        'mailgun' => [
            'adapter' => \STS\EmailEvents\Adapters\Mailgun::class,
            'auth' => env('MAIL_EVENTS_MAILGUN_AUTH', 'token')
        ]
    ]
];