<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;
use App\Contracts\GetTicketInterface;

class RelayCommand extends BaseCommand implements GetTicketInterface
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

    public function getTicket()
    {
        return $this->sms->origin->tickets->last();
    }

    function getSMS()
    {
        return $this->sms;
    }
}
