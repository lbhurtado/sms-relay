<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Support;
use App\CommandBus\Commands\SupportCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SupportHandler
{
    use DispatchesJobs;

    /**
     * @param SupportCommand $command
     */
    public function handle(SupportCommand $command)
    {
        $this->dispatch(new Support($command->origin, $command->message));//TODO add feedback to subscriber
    }
}
