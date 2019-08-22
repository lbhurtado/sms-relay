<?php

namespace App\CommandBus;

use App\CommandBus\Commands\ListenCommand;
use App\CommandBus\Handlers\ListenHandler;

class ListenAction extends BaseAction
{
    protected $permission = 'issue command';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->catchHashtags($data);
    }

    protected function catchHashtags(array $data)
    {
        $this->bus->dispatch(ListenCommand::class, $data);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ListenCommand::class, ListenHandler::class);
    }
}
