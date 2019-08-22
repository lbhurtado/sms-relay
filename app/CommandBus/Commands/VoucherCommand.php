<?php

namespace App\CommandBus\Commands;

/**
 * Class VoucherCommand
 * @package App\CommandBus\Commands
 */
class VoucherCommand
{
	public $origin;

	public $code;

	public $email;

    /**
     * VoucherCommand constructor.
     */
    public function __construct($origin, $code, $email)
    {
    	$this->mobile = $mobile;
    	$this->code = $code;
    	$this->email = $email;
    }
}
