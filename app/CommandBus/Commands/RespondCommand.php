<?php

namespace App\CommandBus\Commands;

use App\{Contact, Ticket};
use App\Contracts\GetTicketInterface;

class RespondCommand extends BaseCommand implements GetTicketInterface
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $ticket_id;

    /** @var string */
    public $message;

    /**
     * RespondCommand constructor.
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
}
