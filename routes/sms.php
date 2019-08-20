<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction};

$router = resolve('missive:router');

$router->register('{message}', app(RelayAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

$router->register('PING', app(PingAction::class));
