<?php

namespace App\CommandBus;

use App\CommandBus\Commands\VoucherCommand;
use App\CommandBus\Handlers\VoucherHandler;

class VoucherAction extends TemplateAction
{
    protected $permission = 'send message';
    protected $command = VoucherCommand::class;
    protected $handler = VoucherHandler::class;
}
