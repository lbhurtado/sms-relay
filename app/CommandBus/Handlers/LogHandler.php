<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\LogCommand;

class LogHandler
{
    /**
     * @param LogCommand $command
     */
    public function handle(LogCommand $command)
    {
        \Log::info($command->sms);
    }
}
