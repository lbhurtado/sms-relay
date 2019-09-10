<?php

namespace App\CommandBus\Commands;

use App\Contact;
use App\Contracts\GetTicketInterface;

class ApproachCommand extends BaseCommand implements GetTicketInterface
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $message;

    /**
     * ApproachCommand constructor.
     *
     * @param Contact $origin
     * @param string $message
     */
    public function __construct(Contact $origin, string $message)
    {
        $this->origin = $origin;
        $this->message = $message;
    }

    public function getTicket()
    {
        $this->origin->load('tickets');

        return $this->origin->tickets->last();
    }

    public function getSMS()
    {
        $this->origin->load('smss');

        return $this->origin->smss->last();
    }
}
