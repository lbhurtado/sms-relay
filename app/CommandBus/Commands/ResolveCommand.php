<?php

namespace App\CommandBus\Commands;

use App\{Contact, Ticket};
use App\Contracts\CommandTicketable;

class ResolveCommand extends BaseCommand implements CommandTicketable
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $ticket_id;

    /** @var string */
    public $message;

    /**
     * ResolveCommand constructor.
     *
     * @param Contact $origin
     * @param string $ticket_id
     * @param string $message
     */
    public function __construct(Contact $origin, string $ticket_id, string $message)
    {
        $this->origin = $origin;
        $this->ticket_id = $ticket_id;
        $this->message = $message;
    }

    public function getTicket()
    {
        return Ticket::fromHash($this->ticket_id);
    }

    public function getSMS()
    {
        return $this->origin->smss->last();
    }
}
