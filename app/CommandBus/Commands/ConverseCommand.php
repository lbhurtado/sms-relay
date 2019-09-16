<?php

namespace App\CommandBus\Commands;

use App\Contact;

class ConverseCommand extends RespondCommand
{
    /**
     * ConverseCommand constructor.
     *
     * @param Contact $origin
     * @param string $ticket_id
     * @param string $message
     */
    public function __construct(Contact $origin, string $ticket_id, string $msg)
    {
    	parent::__construct($origin, $ticket_id, $msg);
    }
}
