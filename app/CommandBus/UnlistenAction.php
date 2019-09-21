<?php

namespace App\CommandBus;

use App\CommandBus\Commands\UnlistenCommand;
use App\CommandBus\Handlers\UnlistenHandler;

class UnlistenAction extends TemplateAction
{
    protected $permission = 'issue command';

    protected $command = UnlistenCommand::class;

    protected $handler = UnlistenHandler::class;
}
