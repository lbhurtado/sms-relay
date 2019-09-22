<?php

namespace App\CommandBus;

use App\CommandBus\Commands\ApproachCommand;
use App\CommandBus\Handlers\ApproachHandler;
use App\Exceptions\{TicketExistsException, MaximumApproachesReachedException};
use App\CommandBus\Middlewares\{CheckTicketExistsMiddleware, CheckMaximumApproachesReachedMiddleware, RecordDiscussionMiddleware};

class ApproachAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = ApproachCommand::class;

    protected $handler = ApproachHandler::class;

    protected $middlewares = [
        CheckTicketExistsMiddleware::class,
        CheckMaximumApproachesReachedMiddleware::class,
        RecordDiscussionMiddleware::class
    ];

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (TicketExistsException | MaximumApproachesReachedException $e) {
            return app(ConverseAction::class)(...$this->arguments);
        }
    }
}
