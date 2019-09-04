<?php

namespace App\CommandBus\Commands;

use App\Contact;

class VoucherCommand extends BaseCommand
{
    /** @var Contact */
    public $origin;

    /** @var int */
    public $pin;

    /**
     * VoucherCommand constructor.
     * @param Contact $origin
     * @param int $pin
     */
    public function __construct(Contact $origin, int $pin)
    {
        $this->origin = $origin;
        $this->pin = $pin;
    }

}
