<?php

namespace App\Events;

class TicketEvents
{
    const OPENED    = 'ticket.opened';
    const ENDORSED  = 'ticket.endorsed';
    const CONVERSED = 'ticket.conversed';
    const UPDATED   = 'ticket.updated';
    const RESOLVED  = 'ticket.resolved';
    const CLOSED    = 'ticket.closed';
}
