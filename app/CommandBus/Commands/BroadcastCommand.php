<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class BroadcastCommand
{
    /*
     * @var string
     */
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