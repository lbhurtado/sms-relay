<?php

namespace App\CommandBus\Middlewares;

use App\Classes\SupportStage;
use League\Tactician\Middleware;

class Statuses implements Middleware
{
    const DISALLOWED_STAGES = [SupportStage::RESOLVED, SupportStage::CLOSED];

    public function execute($command, callable $next)
    {
        if ($ticket = $command->getTicket()) {
            if (in_array($ticket->status, self::DISALLOWED_STAGES))  {
                return false;
            }
        }

        $next($command);
    }
}
