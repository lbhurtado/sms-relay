<?php

namespace App\CommandBus;

use App\CommandBus\Commands\TicketCommand;
use App\CommandBus\Handlers\TicketHandler;

class TicketAction extends BaseAction
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
        $this->bus->dispatch(TicketCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(TicketCommand::class, TicketHandler::class);
    }
}
