<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RedeemCommand;
use App\CommandBus\Handlers\RedeemHandler;

class RedeemAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = RedeemCommand::class;

    protected $handler = RedeemHandler::class;
}
