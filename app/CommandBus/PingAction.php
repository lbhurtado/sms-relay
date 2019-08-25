<?php

namespace App\CommandBus;

use App\Contact;
use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;
use App\CommandBus\Middlewares\LogMiddleware;

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
        $this->bus->dispatch(PingCommand::class, compact('origin'), $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }
}
