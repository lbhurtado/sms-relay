<?php

namespace App\CommandBus;

use Joselfonseca\LaravelTactician\CommandBusInterface;
use App\CommandBus\Commands\{CreateContactCommand, SendMessageCommand, SendMailCommand};
use App\CommandBus\Handlers\{CreateContactHandler, SendMessageHandler, SendMailHandler};

class SendAction
{
    protected $bus;

    public function __construct()
    {
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(CreateContactCommand::class, CreateContactHandler::class);
        $this->bus->addHandler(SendMessageCommand::class, SendMessageHandler::class);
        $this->bus->addHandler(SendMailCommand::class, SendMailHandler::class);
    }

    public function createContact(array $data = [])
    {
        $this->bus->dispatch(CreateContactCommand::class, $data);

        return $this;
    }

    public function sendMessage(array $data = [])
    {
        $this->bus->dispatch(SendMessageCommand::class, $data);

        return $this;
    }

    public function sendMail(array $data = [])
    {
        $this->bus->dispatch(SendMailCommand::class, $data);

        return $this;
    }
}
