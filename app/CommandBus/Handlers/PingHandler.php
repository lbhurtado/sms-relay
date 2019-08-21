<?php

namespace App\CommandBus\Handlers;

use App\Notifications\Pong;
use App\CommandBus\Commands\PingCommand;

class PingHandler
{
    /**
     * @param PingCommand $command
     */
    public function handle(PingCommand $command)
    {
        $command->origin->notify(new Pong);
    }
}
