<?php

namespace App\CommandBus;

use App\Contact;
use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;

class PingAction extends BaseAction
{
    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function ($contact) {
            $this->sendReply($contact);
        });
    }

    public function sendReply(Contact $origin)
    {
        $this->bus->dispatch(PingCommand::class, compact('origin'));

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }
}