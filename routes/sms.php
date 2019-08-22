<?php

use App\CommandBus\{PingAction, BroadcastAction, RelayAction};

$router = resolve('missive:router');

$router->register('{message}', app(RelayAction::class));

$router->register('BROADCAST {message}', app(BroadcastAction::class));

$router->register('PING', app(PingAction::class));

$allowed = config('vouchers.characters');
$email = '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})';
$router->register("{code=([{$allowed}]{4})-([{$allowed}]{4})} {email=({$email})}", function (string $path, array $values) use ($allowed, $router){

    extract($values);

	$contact = $router->missive->getSMS()->origin;
	$voucher = $contact->redeemCode($code);
	$role = $voucher->model;
	$contact->syncRoles($role);
	\Log::info($contact);
});