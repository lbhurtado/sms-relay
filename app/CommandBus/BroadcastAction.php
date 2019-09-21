<?php

namespace App\CommandBus;

use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;
use App\CommandBus\Middlewares\CheckCreditsMiddleware;

class BroadcastAction extends TemplateAction
{
    protected $permission = 'send broadcast';

    protected $command = BroadcastCommand::class;

    protected $handler = BroadcastHandler::class;

    protected $middlewares = [
        CheckCreditsMiddleware::class
    ];
}
