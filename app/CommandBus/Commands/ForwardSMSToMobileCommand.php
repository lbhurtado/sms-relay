<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ForwardSMSToMobileCommand extends BaseCommand
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
