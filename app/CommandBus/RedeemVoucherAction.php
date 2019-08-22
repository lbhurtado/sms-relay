<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RedeemVoucherCommand;
use App\CommandBus\Handlers\RedeemVoucherHandler;

class RedeemVoucherAction extends BaseAction
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
        $this->bus->dispatch(RedeemVoucherCommand::class, $data);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RedeemVoucherCommand::class, RedeemVoucherHandler::class);
    }
}
