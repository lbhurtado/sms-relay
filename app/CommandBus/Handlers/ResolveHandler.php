<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Resolve;
use App\CommandBus\Commands\ResolveCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ResolveHandler
{
    use DispatchesJobs;

    /**
     * @param ResolveCommand $command
     */
    public function handle(ResolveCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            $this->dispatch(new Resolve($contact, $command->ticket_id, $command->message));
        });
    }
}
