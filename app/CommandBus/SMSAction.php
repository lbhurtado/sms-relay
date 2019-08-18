<?php

namespace App\CommandBus;

use Joselfonseca\LaravelTactician\CommandBusInterface;
use App\CommandBus\Commands\{CreateContactCommand, SendMessageCommand, MailSMSCommand};
use App\CommandBus\Handlers\{CreateContactHandler, SendMessageHandler, MailSMSHandler};

class SMSAction
{
    protected $bus;

    public function __construct()
    {
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(CreateContactCommand::class, CreateContactHandler::class);
        $this->bus->addHandler(SendMessageCommand::class, SendMessageHandler::class);
        $this->bus->addHandler(MailSMSCommand::class, MailSMSHandler::class);
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

    public function mailSMS(array $data = [])
    {
        $this->bus->dispatch(MailSMSCommand::class, $data);

        return $this;
    }
}
