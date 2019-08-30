<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Unlisten;
use App\CommandBus\Commands\UnlistenCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UnlistenHandler
{
    use DispatchesJobs;

    /**
     * @param UnlistenCommand $command
     */
    public function handle(UnlistenCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            $this->dispatch(new Unlisten($contact, $command->tags));
        });
    }
}
