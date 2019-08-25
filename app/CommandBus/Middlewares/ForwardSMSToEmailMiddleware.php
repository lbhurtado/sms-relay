<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Notifications\SMSArrived;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\Notification;

class ForwardSMSToEmailMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $emails = Setting::get('forwarding.emails');
        Notification::route('mail', $emails)
            ->notify(new SMSArrived($command->sms));

        $next($command);
    }
}
