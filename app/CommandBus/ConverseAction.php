<?php

namespace App\CommandBus;

use App\Classes\NextRoute;
use App\CommandBus\Middlewares\Statuses;
use App\Exceptions\MaximumApproachesReached;
use App\CommandBus\Commands\ConverseCommand;
use App\CommandBus\Handlers\ConverseHandler;
use App\CommandBus\Middlewares\ConfineMiddleware;
use App\CommandBus\Middlewares\ConverseMiddleware;
use App\Exceptions\{CaseResolvedException, NoTicketException};

class ConverseAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        try {
            $this->converse($this->addHashToData($data));
        }
        catch (CaseResolvedException $e) {
            return NextRoute::GO;
        }

        catch (NoTicketException $e) {
            return NextRoute::GO;
        }
    }

    protected function converse(array $data)
    {
        return $this->bus->dispatch(ConverseCommand::class, $data, [
            ConfineMiddleware::class,
            Statuses::class,
            ConverseMiddleware::class
        ]);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ConverseCommand::class, ConverseHandler::class);
    }

    private function addHashToData($data)
    {
        $lastTicket = $data['origin']->tickets->last();

        if ($lastTicket == null) {
            throw new NoTicketException('Contact has no tickets.');
        } 

        $ticket_id = $data['origin']->tickets->last()->ticket_id;

        return array_merge($data, compact('ticket_id'));
    }
}
