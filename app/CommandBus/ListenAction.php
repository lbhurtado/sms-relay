<?php

namespace App\CommandBus;

use App\CommandBus\Commands\ListenCommand;
use App\CommandBus\Handlers\ListenHandler;

class ListenAction extends TemplateAction
{
    protected $permission = 'issue command';

    protected $command = ListenCommand::class;

    protected $handler = ListenHandler::class;
}
