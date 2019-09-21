<?php

namespace App\CommandBus\Commands;

use LBHurtado\Missive\Models\SMS;
use App\Contracts\CommandTicketable;

class RelayCommand extends BaseCommand implements CommandTicketable
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
