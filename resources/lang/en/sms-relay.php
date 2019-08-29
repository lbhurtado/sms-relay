<?php

return [
    'forward' => [
        'mobile' => "sent from::from\nsent to::to\n\n:message",
    ],
    'reply' => [
        'standard' => ':from, we probably received your message - :message',
    ],
    'broadcast' => ":handle, :message - \n:signature",
    'feedback' => "Feedback: :handle, :message - \n:signature",
    'listen' => "Listened: :handle, :message - \n:signature",
    'redeem' => "Redeemed: :handle, :message - \n:signature",
    'relay' => "Relayed: :handle, :message - \n:signature",
];
