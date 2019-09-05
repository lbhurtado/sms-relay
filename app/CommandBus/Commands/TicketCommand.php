<?php

namespace App\CommandBus\Commands;

use App\Contact;

class TicketCommand extends BaseCommand
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $title;

    /** @var string */
    public $message;

    /**
     * BroadcastCommand constructor.
     *
     * @param Contact $origin
     * @param string title
     * @param string $message
     */
    public function __construct(Contact $origin, string $title, string $message)
    {
        $this->origin = $origin;
        $this->title = $title;
        $this->message = $message;
    }
}
