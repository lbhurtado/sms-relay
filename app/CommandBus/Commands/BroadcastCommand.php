<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class BroadcastCommand
{
    /** @var SMS */
    public $message;

    /**
     * BroadcastCommand constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
