<?php

namespace App\CommandBus\Commands;

class SendMessageCommand
{
	/** @var string */
	public $mobile;

	/** @var string */
	public $message;	

    /**
     * SendMessageCommand constructor.
     */
    public function __construct(string $mobile, string $message)
    {
    	$this->mobile = phone($mobile, 'PH')->formatE164();
    	$this->message = trim($message);
    }	
}