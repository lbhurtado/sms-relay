<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RedeemVoucherCommand;
use App\CommandBus\Handlers\RedeemVoucherHandler;

class RedeemVoucherAction extends BaseAction
{
    public function __invoke(string $path, array $values)
    {
        $this->bus->dispatch(RedeemVoucherCommand::class, $this->getData($values));
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RedeemVoucherCommand::class, RedeemVoucherHandler::class);
    }

    protected function getData(array $values)
    {
        $origin = $this->router->missive->getContact();

        return array_merge($values, compact('origin'));
    }
}
