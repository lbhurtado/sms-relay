<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class SendMailCommand
{
    /** @var SMS */
    public $sms;

    /**
     * SendMailCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
