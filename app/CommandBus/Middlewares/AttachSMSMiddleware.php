<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use League\Tactician\Middleware;
use App\Exceptions\NotEnoughCredits;
use App\CommandBus\Commands\{ApproachCommand, RespondCommand};

class AttachSMSMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $next($command);

        optional($this->getTicket($command), function ($ticket) use ($command) {
	        optional($command->origin->smss->last(), function ($sms) use ($ticket) {
	    		$ticket->addSMS($sms);
	    	});
        });
    }

    protected function getTicket($command)
    {
       $ticket = null;

        switch (get_class($command)) {
        	case ApproachCommand::class:
        		$ticket = $command->origin->tickets->last();
        		break;
        	case RespondCommand::class:
        		$ticket = Ticket::fromHash($command->ticket_id);
        		break;
        }

    	return $ticket;
    }
}
