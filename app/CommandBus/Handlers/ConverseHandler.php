<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Converse;
use App\CommandBus\Commands\ConverseCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ConverseHandler
{
    use DispatchesJobs;

    /**
     * @param ConverseCommand $command
     */
    public function handle(ConverseCommand $command)
    {
         tap($command->origin, function ($contact) use ($command) {
             $this->dispatch(new Converse($contact, $command->ticket_id, $command->message));
         });
    }
}
