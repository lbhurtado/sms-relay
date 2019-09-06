<?php

namespace App\CommandBus;

use App\CommandBus\Commands\SupportCommand;
use App\CommandBus\Handlers\SupportHandler;

class SupportAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->generateTicket($data);
    }

    protected function generateTicket(array $data)
    {
        $this->bus->dispatch(SupportCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(SupportCommand::class, SupportHandler::class);
    }
}
