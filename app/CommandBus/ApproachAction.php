<?php

namespace App\CommandBus;

use App\Exceptions\MaximumApproach;
use App\CommandBus\Middlewares\Statuses;
use App\CommandBus\Middlewares\ConfineMiddleware;
use App\CommandBus\Middlewares\ConverseMiddleware;
use App\CommandBus\Commands\{ApproachCommand, RespondCommand};
use App\CommandBus\Handlers\{ApproachHandler, RespondHandler};

class ApproachAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        try {
            $this->approach($data);
        }
        catch (MaximumApproach $e) {
            $this->respond($this->addHashToData($data));//TODO: if resolved or close, new approach should open ticket
        }
    }

    protected function approach(array $data)
    {
        $this->bus->dispatch(ApproachCommand::class, $data, [
            ConfineMiddleware::class,
            Statuses::class,
            ConverseMiddleware::class
        ]);
    }

    protected function respond(array $data)
    {
        $this->bus->dispatch(RespondCommand::class, $data, [
            Statuses::class,
            ConverseMiddleware::class
        ]);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ApproachCommand::class, ApproachHandler::class);
        $this->bus->addHandler(RespondCommand::class, RespondHandler::class);
    }

    private function addHashToData($data)
    {
        $ticket_id = $data['origin']->tickets->last()->ticket_id;

        return array_merge($data, compact('ticket_id'));
    }
}
