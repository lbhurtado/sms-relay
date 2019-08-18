<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;

class LogCommand
{
	/** @var SMS */
	public $sms;

    /**
     * LogCommand constructor.
     *
     * @param string $message
     */
    public function __construct(SMS $sms)
    {
    	$this->sms = trim($sms);
    }
}
