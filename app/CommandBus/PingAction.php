<?php

namespace App\CommandBus;

use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;

class PingAction extends TemplateAction
{
    protected $command = PingCommand::class;
    protected $handler = PingHandler::class;
}
