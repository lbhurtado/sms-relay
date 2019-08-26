<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Notifications\Arrived;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\Notification;

class EmailMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $emails = Setting::get('forwarding.emails');
        Notification::route('mail', $emails)
            ->notify(new Arrived($command->sms));

        $next($command);
    }
}
