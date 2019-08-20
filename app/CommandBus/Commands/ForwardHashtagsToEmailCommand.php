<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ForwardHashtagsToEmailCommand
{
    /** @var SMS */
    public $sms;

    /**
     * ProcessHashtagsCommand constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
