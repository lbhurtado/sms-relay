<?php

namespace App\CommandBus;

use App\Classes\NextRoute;
use App\Exceptions\CaseResolvedException;
use App\CommandBus\Commands\ResolveCommand;
use App\CommandBus\Handlers\ResolveHandler;
use App\CommandBus\Middlewares\{CheckCaseResolvedMiddleware, RecordDiscussionMiddleware};

class ResolveAction extends TemplateAction
{
    protected $permission = 'issue command';

    protected $command = ResolveCommand::class;

    protected $handler = ResolveHandler::class;

    protected $middlewares = [
        CheckCaseResolvedMiddleware::class, //TODO: test this
        RecordDiscussionMiddleware::class
    ];

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (CaseResolvedException $e) {
            return NextRoute::STOP;
        }
    }
}
