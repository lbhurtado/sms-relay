<?php

return [
    'relay' => [
        'log' => env('RELAY_LOG', true),
        'email' => env('RELAY_EMAIL', true),
        'mobile' => env('RELAY_MOBILE', true),
        'reply' => env('RELAY_REPLY', true),
    ],
];
