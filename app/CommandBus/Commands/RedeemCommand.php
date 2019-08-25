<?php

namespace App\CommandBus\Commands;

class RedeemCommand extends BaseCommand
{
    /**
     * @var \App\Contact
     */
    public $origin;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $email;

    /**
     * VoucherCommand constructor.
     * @param \App\Contact $origin
     * @param string $code
     * @param string $email
     */
    public function __construct(\App\Contact $origin, string $code, string $email)
    {
    	$this->origin = $origin;
    	$this->code = $code;
    	$this->email = $email;
    }
}
