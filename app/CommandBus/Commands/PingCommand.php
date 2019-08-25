<?php

namespace App\CommandBus\Commands;

use App\Contact;

class PingCommand extends BaseCommand
{
    /** @var Contact */
    public $origin;

    /**
     * PingCommand constructor.
     *
     * @param Contact $origin
     */
    public function __construct(Contact $origin)
    {
        $this->origin = $origin;
    }
}
