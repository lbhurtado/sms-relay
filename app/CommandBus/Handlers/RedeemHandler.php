<?php

namespace App\CommandBus\Handlers;

use App\Jobs\RedeemCodeJob;
use App\CommandBus\Commands\RedeemCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RedeemHandler
{
    use DispatchesJobs;

    /**
     * @param RedeemCommand $command
     */
    public function handle(RedeemCommand $command)
    {
        $this->dispatch(new RedeemCodeJob($command->origin, $command->code, $command->email));
    }
}
