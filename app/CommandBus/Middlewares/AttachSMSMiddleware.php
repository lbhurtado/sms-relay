<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use League\Tactician\Middleware;
use App\Contracts\GetTicketInterface;
use App\CommandBus\Commands\{ApproachCommand, RespondCommand};

class AttachSMSMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $next($command);

        optional($command->getTicket(), function ($ticket) use ($command) {
	        optional($command->origin->smss->last(), function ($sms) use ($ticket) {
	    		$ticket->addSMS($sms);
	    	});
        });
    }
}
