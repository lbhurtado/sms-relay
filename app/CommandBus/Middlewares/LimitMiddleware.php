<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;

class LimitMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $usage = $command->origin->usage;
        $balance = $command->origin->balance;

        if (! $usage > $balance)
            throw new \Exception("Balance of {$balance} is less than the usage {$usage}.");

        $next($command);
    }
}
