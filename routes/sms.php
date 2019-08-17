<?php

use LBHurtado\EngageSpark\Notifications\Adhoc;

$router = resolve('missive:router');

$router->register('LOG {message}', function (string $path, array $values) use ($router) {
    \Log::info($values['message']);

    tap($router->missive->getSMS()->origin, function ($contact) use ($values) {
        $message = $values['message'];
        $contact->notify(new Adhoc("{$contact->mobile}: $message"));
    });
});
