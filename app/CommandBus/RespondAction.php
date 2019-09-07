<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RespondCommand;
use App\CommandBus\Handlers\RespondHandler;

class RespondAction extends BaseAction
{
    protected $permission = 'issue command';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->updateTicket($data);
    }

    protected function updateTicket(array $data)
    {
        $this->bus->dispatch(RespondCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RespondCommand::class, RespondHandler::class);
    }
}
