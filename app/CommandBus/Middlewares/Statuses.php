<?php

namespace App\CommandBus\Middlewares;

use App\Classes\SupportStage;
use League\Tactician\Middleware;
use App\Exceptions\CaseResolvedException;

class Statuses implements Middleware
{
    const DISALLOWED_STAGES = [SupportStage::RESOLVED, SupportStage::CLOSED];

    public function execute($command, callable $next)
    {
        if ($ticket = $command->getTicket()) {
            if (in_array($ticket->status, self::DISALLOWED_STAGES))  {
                throw new CaseResolvedException("Case Resolved!");
            }
        }

        $next($command);
    }
}
