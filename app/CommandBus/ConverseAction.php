<?php

namespace App\CommandBus;

use App\CommandBus\Middlewares\Statuses;
use App\Exceptions\MaximumApproachesReached;
use App\CommandBus\Commands\ConverseCommand;
use App\CommandBus\Handlers\ConverseHandler;
use App\CommandBus\Middlewares\ConfineMiddleware;
use App\CommandBus\Middlewares\ConverseMiddleware;

class ConverseAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->converse($this->addHashToData($data));
        // try {
        //     $this->approach($data);
        // }
        // catch (MaximumApproachesReached $e) {
        //     $this->respond($this->addHashToData($data));
        // }
    }

    protected function converse(array $data)
    {
        $this->bus->dispatch(ConverseCommand::class, $data, [
            // ConfineMiddleware::class,
            // Statuses::class,
            // ConverseMiddleware::class
        ]);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ConverseCommand::class, ConverseHandler::class);
    }

    private function addHashToData($data)
    {
        $ticket_id = $data['origin']->tickets->last()->ticket_id;

        return array_merge($data, compact('ticket_id'));
    }
}
