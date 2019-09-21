<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use League\Tactician\Middleware;
use App\Contracts\CommandTicketable;
use App\Exceptions\CaseResolvedException;

class CheckCaseResolvedMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        if ($command instanceof CommandTicketable) {
            if ($ticket = $command->getTicket()) {
                if (in_array($ticket->status, Ticket::CONSIDERED_RESOLVED_STAGES)) {
                    throw new CaseResolvedException("Case is resolved!");//TODO: put this in language file
                }
            }
        }

        $next($command);
    }
}
