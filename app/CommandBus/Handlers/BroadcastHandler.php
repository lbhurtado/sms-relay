<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\BroadcastCommand;
use LBHurtado\EngageSpark\Notifications\Adhoc;
use LBHurtado\Missive\Repositories\ContactRepository;

class BroadcastHandler
{
    protected $contacts;

    /**
     * BroadcastHandler constructor.
     * @param ContactRepository $contacts
     */
    public function __construct(ContactRepository $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * @param BroadcastCommand $command
     */
    public function handle(BroadcastCommand $command)
    {
        $this->contacts->all()->each(function ($contact) use ($command) {
            $contact->notify(new Adhoc($command->message));
        });
    }
}
