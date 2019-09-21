<?php

namespace App\CommandBus;

use App\CommandBus\Commands\PostCommand;
use App\CommandBus\Handlers\PostHandler;

class PostAction extends TemplateAction
{
    protected $permission = 'send broadcast';

    protected $command = PostCommand::class;

    protected $handler = PostHandler::class;
}
