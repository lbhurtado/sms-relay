<?php

namespace App\CommandBus;

use App\CommandBus\Commands\{ApproachCommand, ConverseCommand};
use App\CommandBus\Handlers\{ApproachHandler, ConverseHandler};
use App\Exceptions\{MaximumApproachesReachedException, CaseResolvedException};
use App\CommandBus\Middlewares\{CheckApproachesMiddleware, ConverseMiddleware, CheckResolvedMiddleware};

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
        catch (CaseResolvedException $e) {
            $this->approach_again($data);
        }
        catch (MaximumApproachesReachedException $e) {
            $this->just_converse($data);
        }
    }

    protected function approach(array $data)
    {
        $this->bus->dispatch(ApproachCommand::class, $data, [
            CheckApproachesMiddleware::class,
            CheckResolvedMiddleware::class,
            ConverseMiddleware::class
        ]);
    }

    protected function approach_again(array $data)
    {
        $this->bus->dispatch(ApproachCommand::class, $data, [
            CheckApproachesMiddleware::class,
            ConverseMiddleware::class
        ]);
    }

    protected function just_converse(array $data)
    {
        $data = $this->addMsgToData($this->addHashToData($data));

        $this->bus->dispatch(ConverseCommand::class, $data, [
            CheckResolvedMiddleware::class,
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
