<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Exceptions\NotEnoughCredits;

class CheckCreditsMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $usage = $command->origin->usage;
        $balance = $command->origin->balance;

        //TODO get projected usage instead
        if ($usage > $balance){
            throw new NotEnoughCredits("Balance of {$balance} is less than the usage {$usage}.");
        }

        $next($command);
    }
}
