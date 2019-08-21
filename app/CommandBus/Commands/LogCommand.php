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
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
    	$this->sms = $sms;
    }
}