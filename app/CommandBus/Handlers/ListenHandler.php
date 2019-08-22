<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\ListenCommand;

class ListenHandler
{
    /**
     * @param ListenCommand $command
     */
    public function handle(ListenCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            $contact->catch($this->getHashtags($command));
        });
    }

    protected function getHashtags(ListenCommand $command)
    {
        return explode(' ', $command->tags);
    }
}
