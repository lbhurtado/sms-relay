<?php

namespace App\CommandBus\Commands;

use App\Contact;

class RedeemCommand
{
    /**
     * @var Contact
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
     * @param Contact $origin
     * @param string $code
     * @param string $email
     */
    public function __construct(Contact $origin, string $code, string $email)
    {
    	$this->origin = $origin;
    	$this->code = $code;
    	$this->email = $email;
    }
}
