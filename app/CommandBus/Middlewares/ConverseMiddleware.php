<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Contracts\GetTicketInterface;

class ConverseMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $next($command);

        $this->addSMS($command);
    }

    protected function addSMS(GetTicketInterface $command)
    {
        optional($command->getTicket(), function ($ticket) use ($command) {
            optional($command->origin->smss->last(), function ($sms) use ($ticket) {
                $ticket->addSMS($sms);
            });
        });
    }
}
