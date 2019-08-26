<?php

namespace App\CommandBus\Handlers;

use App\Notifications\Listened;
use App\CommandBus\Commands\ListenCommand;

class ListenHandler
{
    /**
     * @param ListenCommand $command
     */
    public function handle(ListenCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            if ($hashtags = $this->getHashtags($command)) {
                $contact->catch($this->getHashtags($command));
                $contact->notify(new Listened);
            }
        });
    }

    protected function getHashtags(ListenCommand $command)
    {
        return explode(' ', $command->tags);
    }
}
