<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Notifications\Acknowledged;

class ReplyMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $command->sms->origin->notify(new Acknowledged);

        $next($command);
    }
}
