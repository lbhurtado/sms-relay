<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ForwardSMSToMobileCommand
{
    /** @var SMS */
    public $sms;

    /**
     * ForwardSMSToMobileCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
