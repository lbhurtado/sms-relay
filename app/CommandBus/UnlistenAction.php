<?php

namespace App\CommandBus;

use App\CommandBus\Commands\UnlistenCommand;
use App\CommandBus\Handlers\UnlistenHandler;

class UnlistenAction extends BaseAction
{
    protected $permission = 'issue command';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->uncatchHashtags($data);
    }

    protected function uncatchHashtags(array $data)
    {
        $this->bus->dispatch(UnlistenCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(UnlistenCommand::class, UnlistenHandler::class);
    }
}
