<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction, RedeemAction, ListenAction};

$router = resolve('missive:router');

$router->register('{message}', app(RelayAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

$router->register('PING', app(PingAction::class));

$router->register('LISTEN {tags}', app(ListenAction::class));

$regex_code = '';
tap(config('vouchers.characters'), function ($allowed) use (&$regex_code) {
    $regex_code = "([{$allowed}]{4})-([{$allowed}]{4})";
});
$regex_email = '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})';
$router->register("{code={$regex_code}} {email={$regex_email}}", app(RedeemAction::class));
