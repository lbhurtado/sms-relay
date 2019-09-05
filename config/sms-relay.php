<?php

return [
    'relay' => [
        'log'       => env('RELAY_LOG',       true),
        'email'     => env('RELAY_EMAIL',     true),
        'mobile'    => env('RELAY_MOBILE',    true),
        'reply'     => env('RELAY_REPLY',     true),
        'hashtags'  => env('RELAY_HASHTAGS',  true),
    ],
    'broadcast' => [
        'keyword'   => 'BROADCAST',
        'optional'  => env('BROADCAST_OPTIONAL', true),
    ],
    'permissions' => [
        'spokesman'  => ['send message', 'issue command', 'send broadcast'],
        'listener'   => ['issue command'],
        'subscriber' => ['send message'],
        'forwarder'  => ['issue command'],
    ],
    'vouchers' => [
        'spokesman'  => env('SPOKESMAN_VOUCHERS', 1),
        'listener'   => env('LISTENER_VOUCHERS', 5),
        'forwarder'   => env('FORWARDER_VOUCHERS', 2),
    ],
    'signature' => env('SIGNATURE', 'SMS Relay'),
    'credits' => [
        'initial' => [
            'spokesman' => env('SPOKESMAN_CREDIT', 10),
        ],
    ]
];
