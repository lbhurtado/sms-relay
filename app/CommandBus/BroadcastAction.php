<?php

namespace App\CommandBus;

use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;

class BroadcastAction extends BaseAction
{
    protected $permission = 'send broadcast';

    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function($origin) use ($values) {
            $this->broadcastMessage(array_merge($values, compact('origin')));
        });
    }

    /**
     * @param array $data
     * e.g. $data = ['message' => 'The quick brown fox...']
     * @return $this
     */
    public function broadcastMessage(array $data = [])
    {
        $this->bus->dispatch(BroadcastCommand::class, $data, $this->middlewares);

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(BroadcastCommand::class, BroadcastHandler::class);
    }
}
