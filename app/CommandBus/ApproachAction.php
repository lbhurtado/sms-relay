<?php

namespace App\CommandBus;

use App\Classes\Hash;
use Illuminate\Support\Arr;
use App\CommandBus\Commands\ApproachCommand;
use App\CommandBus\Handlers\ApproachHandler;
use App\Exceptions\{TicketExistsException, MaximumApproachesReachedException, CaseResolvedException};
use App\CommandBus\Middlewares\{CheckTicketExistsMiddleware, CheckMaximumApproachesReachedMiddleware, RecordDiscussionMiddleware, CheckCaseResolvedMiddleware};

class ApproachAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = ApproachCommand::class;

    protected $handler = ApproachHandler::class;

    protected $middlewares = [
        CheckTicketExistsMiddleware::class,
        CheckMaximumApproachesReachedMiddleware::class,
        CheckCaseResolvedMiddleware::class,
        RecordDiscussionMiddleware::class
    ];

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (CaseResolvedException $e) {
            $this->removeChecks();

            return parent::dispatchHandlers();
        }
        catch (TicketExistsException | MaximumApproachesReachedException $e) {
            $this->addHashToData()->sanitizeArguments();

            return app(ConverseAction::class)(...$this->arguments);
        }
    }

    protected function sanitizeArguments(): void
    {
        Arr::set($this->arguments, '1.msg', Arr::get($this->arguments, '1.message'));
        unset($this->arguments[1]['message']);

        array_push($this->arguments, Arr::wrap(ConverseAction::NO_CHECK_APPROACH));
    }

    protected function removeChecks(): void
    {
        Arr::forget($this->middlewares, array_search(CheckCaseResolvedMiddleware::class, $this->middlewares));
    }

    public function setup()
    {
        parent::setup();

        $this->addHashToData();
    }

    protected function addHashToData()
    {
        $ticket_id = optional($this->data['origin']->tickets->last())->ticket_id ?? Hash::EMPTY;

        $this->data = array_merge($this->data, compact('ticket_id'));

        return $this;
    }

//    public function __invoke(string $path, array $values)
//    {
//        if (! $origin = $this->permittedContact()) return;
//
//        $data = array_merge($values, compact('origin'));
//
//        try {
//            $this->approach($data);
//        }
//        catch (CaseResolvedException $e) {
//            $this->approach_again($data);
//        }
//        catch (MaximumApproachesReachedException $e) {
//            $this->just_converse($data);
//        }
//    }
//
//    protected function approach(array $data)
//    {
//        $this->bus->dispatch(ApproachCommand::class, $data, [
//            CheckApproachesMiddleware::class,
//            CheckResolvedMiddleware::class,
//            ConverseMiddleware::class
//        ]);
//    }
//
//    protected function approach_again(array $data)
//    {
//        $this->bus->dispatch(ApproachCommand::class, $data, [
//            CheckApproachesMiddleware::class,
//            ConverseMiddleware::class
//        ]);
//    }
//
//    protected function just_converse(array $data)
//    {
//        $data = $this->addMsgToData($this->addHashToData($data));
//
//        $this->bus->dispatch(ConverseCommand::class, $data, [
//            CheckResolvedMiddleware::class,
//            ConverseMiddleware::class
//        ]);
//    }
//
//    protected function addBusHandlers()
//    {
//        $this->bus->addHandler(ApproachCommand::class, ApproachHandler::class);
//        $this->bus->addHandler(ConverseCommand::class, ConverseHandler::class);
//    }
//
//    private function addHashToData($data)
//    {
//        $ticket_id = $data['origin']->tickets->last()->ticket_id;
//
//        return array_merge($data, compact('ticket_id'));
//    }
//
//    private function addMsgToData($data)
//    {
//        $msg = $data['message'];
//
//        return array_merge($data, compact('msg'));
//    }
}
