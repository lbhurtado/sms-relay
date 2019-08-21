<?php

namespace App\CommandBus;

use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;

class PingAction extends BaseAction
{
    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function ($contact) {
            $this->sendReply($contact->mobile);
        });
    }

    public function sendReply(string $mobile)
    {
        $this->bus->dispatch(PingCommand::class, compact('mobile'));

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }
}
