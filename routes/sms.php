<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction, RedeemVoucherAction};

$router = resolve('missive:router');

$router->register('{message}', app(RelayAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

$router->register('PING', app(PingAction::class));

$router->register('{code=(.*)-(.*)} {email=(.*)}', app(RedeemVoucherAction::class));

//$allowed = config('vouchers.characters');
//$email = '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})';
