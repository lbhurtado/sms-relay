<?php

use App\Mail\SendMailable;
use Illuminate\Support\Facades\Mail;
use LBHurtado\EngageSpark\Notifications\Adhoc;

$router = resolve('missive:router');

$router->register('LOG {message}', function (string $path, array $values) use ($router) {
    \Log::info($values['message']);

    tap($router->missive->getSMS()->origin, function ($contact) use ($values) {
        $message = $values['message'];
        $contact->notify(new Adhoc("{$contact->mobile}: $message"));
    });
});

$router->register('{message}', function (string $path, array $values) use ($router) {
    tap($router->missive->getSMS()->origin, function ($contact) use ($values) {
        $mobile = $contact->mobile;
        $message = $values['message'];

        Mail::to('lester@3rd.tel')->send(new SendMailable($mobile, $message));
    });

    return false;
});


