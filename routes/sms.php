<?php

use App\CommandBus\{PingAction, SMSAction, LogAction, MailSMSAction, BroadcastAction, HashtagsAction};

$router = resolve('missive:router');

$router->register('LOG {message}', app(LogAction::class));

$router->register('PING', app(PingAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

//$router->register('{message}', app(MailSMSAction::class));
$router->register('{message}', app(HashtagsAction::class));


