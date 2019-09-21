<?php

namespace App\CommandBus\Middlewares;

use App\Contact;
use League\Tactician\Middleware;
use App\Exceptions\TicketExistsException;

class CheckTicketExistsMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        if ($this->getUnresolvedTicketCount($command->origin) > 0) {
            throw new TicketExistsException('Contact has unresolved tickets.');
        }

        $next($command);
    }

    protected function getUnresolvedTicketCount(Contact $origin)
    {
        return $origin->tickets()->consideredNotResolved()->count();
    }
}
