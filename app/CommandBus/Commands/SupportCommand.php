<?php

namespace App\CommandBus\Commands;

use App\Contact;

class SupportCommand extends BaseCommand
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $message;

    /**
     * SupportCommand constructor.
     *
     * @param Contact $origin
     * @param string $message
     */
    public function __construct(Contact $origin, string $message)
    {
        $this->origin = $origin;
        $this->message = $message;
    }
}
