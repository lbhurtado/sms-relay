<?php

namespace App\CommandBus;

use App\Classes\NextRoute;
use App\CommandBus\Middlewares\Statuses;
use App\Exceptions\{MaximumApproachesReached, CaseResolvedException};
use App\CommandBus\Middlewares\ConfineMiddleware;
use App\CommandBus\Middlewares\ConverseMiddleware;
use App\CommandBus\Commands\{ApproachCommand, ConverseCommand};
use App\CommandBus\Handlers\{ApproachHandler, ConverseHandler};

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
        catch (MaximumApproachesReached $e) {
            $this->respond($this->addMsgToData($this->addHashToData($data)));//TODO: if resolved or close, new approach should open ticket
        }
        catch (CaseResolvedException $e) {
            $this->reapproach($data);
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

    protected function reapproach(array $data)
    {
        $this->bus->dispatch(ApproachCommand::class, $data, [
            ConfineMiddleware::class,
            ConverseMiddleware::class
        ]);
    }

    protected function respond(array $data)
    {
        $this->bus->dispatch(ConverseCommand::class, $data, [
            Statuses::class,
            ConverseMiddleware::class
        ]);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ApproachCommand::class, ApproachHandler::class);
        $this->bus->addHandler(ConverseCommand::class, ConverseHandler::class);
    }

    private function addHashToData($data)
    {
        $ticket_id = $data['origin']->tickets->last()->ticket_id;

        return array_merge($data, compact('ticket_id'));
    }

    private function addMsgToData($data)
    {
        $msg = $data['message'];

        return array_merge($data, compact('msg'));
    }
}
