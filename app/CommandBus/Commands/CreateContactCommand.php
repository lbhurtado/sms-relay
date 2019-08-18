<?php

namespace App\CommandBus\Commands;

class CreateContactCommand
{
	/** @var string */
	public $mobile;

    /**
     * CreateContactCommand constructor.
	 *     
     * @param string $mobile
     */
    public function __construct(string $mobile)
    {
    	$this->mobile = phone($mobile, 'PH')->formatE164();
    }
}
