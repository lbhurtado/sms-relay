<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction, RedeemAction, ListenAction, UnlistenAction, PostAction, VoucherAction, SupportAction, RespondAction};

$router = resolve('missive:router'); extract(redeem_regex());

$router->register('{message}', RelayAction::class);

$router->register("{message=(.*)\?}", SupportAction::class);

$router->register('BROADCAST {message}', BroadcastAction::class);

$router->register('POST {message}', PostAction::class);

$router->register('PING', PingAction::class);

$router->register('LISTEN {tags}', ListenAction::class);

$router->register('UNLISTEN {tags}', UnlistenAction::class);

$router->register("VOUCHER {pin}", VoucherAction::class);

$router->register("RESPOND {ticket_id=\w+} {message}", RespondAction::class);

$router->register("{code={$regex_code}} {email={$regex_email}}", RedeemAction::class);
