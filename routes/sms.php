<?php

use App\CommandBus\{PingAction, SMSAction, LogAction, MailSMSAction, BroadcastAction};

$router = resolve('missive:router');

$router->register('{message}', app(MailSMSAction::class));

$router->register('LOG {message}', app(LogAction::class));

$router->register('PING', app(PingAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));



