<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Listen;
use App\CommandBus\Commands\ListenCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ListenHandler
{
    use DispatchesJobs;

    /**
     * @param ListenCommand $command
     */
    public function handle(ListenCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            $this->dispatch(new Listen($contact, $command->tags));
        });
    }
}
