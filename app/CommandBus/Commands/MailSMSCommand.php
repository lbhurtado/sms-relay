<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class MailSMSCommand
{
    /** @var SMS */
    public $sms;

    /**
     * MailSMSCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
