<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ForwardSMSToMailCommand extends BaseCommand
{
    /** @var SMS */
    public $sms;

    /**
     * ForwardSMSToMailCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
