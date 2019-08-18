<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\LogCommand;
use App\CommandBus\Handlers\LogHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class LogAction
{
	protected $bus;

    protected $router;

	public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(LogCommand::class, LogHandler::class);
    }	

    public function __invoke(string $path, array $values)
    {
        $this->logSMS(['sms' => $this->router->missive->getSMS()]);
    }

    public function logSMS(array $data = [])
    {
        $this->bus->dispatch(LogCommand::class, $data);

        return $this;      
    }
}