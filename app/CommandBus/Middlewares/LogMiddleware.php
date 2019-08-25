<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;

class LogMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        \Log::info($command);

        $next($command);
    }
}
