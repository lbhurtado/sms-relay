<?php

namespace App\CommandBus\Commands;

class PingCommand
{
	/** @var string */
	public $mobile;

    /**
     * PingCommand constructor.
     *
     * @param string $mobile
     */
    public function __construct(string $mobile)
    {
    	$this->mobile = phone($mobile, 'PH')->formatE164();
    }
}
