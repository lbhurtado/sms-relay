<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class ProcessHashtagsCommand
{
    /** @var SMS */
    public $sms;

    /**
     * ProcessHashtagsCommand constructor.
     * @param string $message
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }
}
