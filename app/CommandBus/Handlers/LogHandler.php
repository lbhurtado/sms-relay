<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\LogCommand;

class LogHandler
{
    /**
     * LogHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param LogCommand $command
     */
    public function handle(LogCommand $command)
    {
        \Log::info($command->sms);
    }
}
