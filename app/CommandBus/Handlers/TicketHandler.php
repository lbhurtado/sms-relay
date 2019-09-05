<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Ticket;
use App\CommandBus\Commands\TicketCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TicketHandler
{
    use DispatchesJobs;

    /**
     * @param TicketCommand $command
     */
    public function handle(TicketCommand $command)
    {
        $this->dispatch(new Ticket($command->origin, $command->title, $command->message));//TODO add feedback to subscriber
    }
}
