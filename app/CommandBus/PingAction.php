<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class PingAction
{
	protected $bus;

    protected $router;

	public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }	

    public function __invoke(string $path, array $values)
    {
        $mobile = $this->router->missive->getSMS()->origin->mobile;

        $this->sendReply(compact('mobile'));
    }

    public function sendReply(array $data = [])
    {
        $this->bus->dispatch(PingCommand::class, $data);

        return $this;      
    }
}