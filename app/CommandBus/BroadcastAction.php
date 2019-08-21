<?php

namespace App\CommandBus;

use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;

class BroadcastAction extends BaseAction
{
    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function() use ($values) {
            $this->broadcastMessage($values);
        });
    }

    /**
     * @param array $data
     * e.g. $data = ['message' => 'The quick brown fox...']
     * @return $this
     */
    public function broadcastMessage(array $data = [])
    {
        $this->bus->dispatch(BroadcastCommand::class, $data);

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(BroadcastCommand::class, BroadcastHandler::class);
    }
}
