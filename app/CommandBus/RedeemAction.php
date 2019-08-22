<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RedeemCommand;
use App\CommandBus\Handlers\RedeemHandler;

class RedeemAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->assumeListenerRole($data);
    }

    protected function assumeListenerRole(array $data)
    {
        $this->bus->dispatch(RedeemCommand::class, $data);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RedeemCommand::class, RedeemHandler::class);
    }
}
