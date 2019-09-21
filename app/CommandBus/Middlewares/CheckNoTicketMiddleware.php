<?php

namespace App\CommandBus\Middlewares;

use App\Classes\Hash;
use League\Tactician\Middleware;
use App\Exceptions\NoTicketException;

class CheckNoTicketMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        if (Hash::EMPTY == optional($command)->ticket_id) { //TODO: check interface
            throw new NoTicketException('Contact has no tickets.');
        }

        $next($command);
    }
}
