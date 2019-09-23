<?php

namespace App\CommandBus\Middlewares;

use App\Contact;
use League\Tactician\Middleware;
use App\Exceptions\ShouldBroadcastException;

class CheckBroadcasterMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {

        if ($origin = $command->getSMS()->origin) {
            if ($this->shouldBroadcast($origin)) {
                throw new ShouldBroadcastException("Should broadcast.");//TODO: put this in language file
            }
        }

        $next($command);
    }

   protected function shouldBroadcast(Contact $origin)
   {
       return (bool) config('sms-relay.broadcast.optional') && (bool) $origin->hasPermissionTo('send broadcast');
   }
}
