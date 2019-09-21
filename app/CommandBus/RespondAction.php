<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RespondCommand;
use App\CommandBus\Handlers\RespondHandler;
use App\CommandBus\Middlewares\{CheckCaseResolvedMiddleware, RecordDiscussionMiddleware};

class RespondAction extends TemplateAction
{
    protected $permission = 'issue command';

    protected $command = RespondCommand::class;

    protected $handler = RespondHandler::class;

    protected $middlewares = [
        CheckCaseResolvedMiddleware::class, //TODO: test this
        RecordDiscussionMiddleware::class
    ];
}
