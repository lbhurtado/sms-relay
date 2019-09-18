<?php

namespace App\CommandBus;

use App\Contact;
use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;

class PingAction extends BaseAction
{
//    public function __invoke(string $path, array $values)
    public function __invoke(...$parameters)
    {
        dd($parameters);
        optional($this->permittedContact(), function ($contact) {
            $this->sendReply($contact);
        });
    }

    public function sendReply(Contact $origin)
    {
        $this->bus->dispatch(PingCommand::class, compact('origin'), $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }
}
