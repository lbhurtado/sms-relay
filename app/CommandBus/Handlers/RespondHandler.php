<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Respond;
use App\CommandBus\Commands\RespondCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RespondHandler
{
    use DispatchesJobs;

    /**
     * @param RespondCommand $command
     */
    public function handle(RespondCommand $command)
    {
        tap($command->origin, function ($contact) use ($command) {
            $this->dispatch(new Respond($contact, $command->ticket_id, $command->message));
        });
    }
}
