<?php

namespace App\CommandBus\Middlewares;

use App\Contact;
use League\Tactician\Middleware;
use App\Notifications\SMSForwarded;
use Akaunting\Setting\Facade as Setting;

class ForwardMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $mobiles = Setting::get('forwarding.mobiles');
        foreach ($mobiles as $mobile) {
            $contact = Contact::bearing($mobile);
            $contact->notify(new SMSForwarded($command));
        }

        $next($command);
    }
}
