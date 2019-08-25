<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction, RedeemAction, ListenAction};

$router = resolve('missive:router'); extract(redeem_regex());

$router->register('{message}', app(RelayAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

$router->register('PING', app(PingAction::class));

$router->register('LISTEN {tags}', app(ListenAction::class));

$router->register("{code={$regex_code}} {email={$regex_email}}", app(RedeemAction::class));
