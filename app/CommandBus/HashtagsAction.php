<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\ProcessHashtagsCommand;
use App\CommandBus\Handlers\ProcessHashtagsHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class HashtagsAction
{
    protected $bus;

    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(ProcessHashtagsCommand::class, ProcessHashtagsHandler::class);
    }

    public function __invoke(string $path, array $values)
    {
        $this->processMessage($values);
    }

    public function processMessage(array $data = [])
    {
        $this->bus->dispatch(ProcessHashtagsCommand::class, $data);

        return $this;
    }
}
