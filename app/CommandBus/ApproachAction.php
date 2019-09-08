<?php

namespace App\CommandBus;

use App\CommandBus\Commands\ApproachCommand;
use App\CommandBus\Handlers\ApproachHandler;

class ApproachAction extends BaseAction
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
        $this->bus->dispatch(ApproachCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ApproachCommand::class, ApproachHandler::class);
    }
}
