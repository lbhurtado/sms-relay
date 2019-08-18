<?php

use App\CommandBus\{PingAction, SendAction, LogAction, MailAction};

$router = resolve('missive:router');

$router->register('LOG {message}', app(LogAction::class));

$router->register('PING', app(PingAction::class));

$router->register('{message}', app(MailAction::class));


