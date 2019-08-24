<?php

namespace App\CommandBus\Handlers;

use App\Notifications\Broadcast;
use App\Notifications\BroadcastFeedback;
use App\CommandBus\Commands\BroadcastCommand;
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
            $contact->notify(new Broadcast($command->message));
        });
        $command->origin->notify(new BroadcastFeedback);
    }
}
