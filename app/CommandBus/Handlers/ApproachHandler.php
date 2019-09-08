<?php

namespace App\CommandBus\Handlers;

use App\Jobs\Approach;
use App\CommandBus\Commands\ApproachCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ApproachHandler
{
    use DispatchesJobs;

    /**
     * @param ApproachCommand $command
     */
    public function handle(ApproachCommand $command)
    {
        $this->dispatch(new Approach($command->origin, $command->message));
    }
}
