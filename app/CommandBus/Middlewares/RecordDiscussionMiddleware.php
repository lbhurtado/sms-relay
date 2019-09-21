<?php

namespace App\CommandBus\Middlewares;

use League\Tactician\Middleware;
use App\Contracts\CommandTicketable;

class RecordDiscussionMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $next($command);

        $this->addSMS($command);
    }

    protected function addSMS(CommandTicketable $command)
    {
        optional($command->getTicket(), function ($ticket) use ($command) {
            tap($command->getSMS(), function ($sms) use ($ticket) {
                $ticket->addSMS($sms);
            });
        }); //TODO: Test this, solution is to load relations, check Command
    }
}
