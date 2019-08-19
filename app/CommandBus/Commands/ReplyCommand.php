<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ReplyCommand
{
    /** @var SMS */
    public $sms;

    /**
     * ReplyCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
