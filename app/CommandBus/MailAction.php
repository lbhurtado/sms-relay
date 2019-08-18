<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\SendMailCommand;
use App\CommandBus\Handlers\SendMailHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class MailAction
{
    protected $bus;

    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(SendMailCommand::class, SendMailHandler::class);
    }

    public function __invoke(string $path, array $values)
    {
        $sms = $this->router->missive->getSMS();

        $this->forwardMessage(compact('sms'));

        return false; //this is needed to cycle to the other registered routes
    }

    public function forwardMessage(array $data = [])
    {
        $this->bus->dispatch(SendMailCommand::class, $data);

        return $this;
    }
}
