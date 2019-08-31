<?php

namespace App\CommandBus\Handlers;

use App\Notifications\Feedback;
use App\Notifications\Broadcast;
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
        $this->contacts->findWhereNotIn('mobile', [$command->origin->mobile])->each(function ($contact) use ($command) {
            $contact->notify(new Broadcast($command->message));
        });
        $command->origin->notify(new Feedback($command->message));
    }
}
