<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction, RedeemAction, ListenAction, UnlistenAction, PostAction, VoucherAction, ApproachAction, RespondAction, ResolveAction};

$router = resolve('missive:router'); extract(redeem_regex());

$router->register('{message}', RelayAction::class);

$router->register("{message=(.+)\?}", ApproachAction::class);

$router->register('BROADCAST {message}', BroadcastAction::class);

$router->register('POST {message}', PostAction::class);

$router->register('PING', PingAction::class);

$router->register('LISTEN {tags}', ListenAction::class);

$router->register('UNLISTEN {tags}', UnlistenAction::class);

$router->register("VOUCHER {pin}", VoucherAction::class);

$router->register("@{ticket_id=\w{4}} {message}", RespondAction::class);

$router->register("RESOLVE {ticket_id=\w{4}} {message}", ResolveAction::class); //TODO ticket_id must be correct, resolve gets last ticket - wrong

$router->register("{code={$regex_code}} {email={$regex_email}}", RedeemAction::class);
