<?php

namespace App\CommandBus;

use Illuminate\Support\Arr;
use App\Classes\{NextRoute, Hash};
use App\CommandBus\Commands\ConverseCommand;
use App\CommandBus\Handlers\ConverseHandler;
use App\Exceptions\{CaseResolvedException, NoTicketException};
use App\CommandBus\Middlewares\{CheckNoTicketMiddleware, CheckCaseResolvedMiddleware, RecordDiscussionMiddleware};

class ConverseAction extends TemplateAction
{
    public const NO_CHECK_APPROACH = 'no_check_approach';

    protected $permission = 'send message';

    protected $command = ConverseCommand::class;

    protected $handler = ConverseHandler::class;

    protected $middlewares = [
        CheckNoTicketMiddleware::class,
        CheckCaseResolvedMiddleware::class,
        RecordDiscussionMiddleware::class
    ];

    public function dispatchHandlers()
    {
//        if (array_key_exists(2, $this->arguments)) {
//            if (in_array(self::NO_CHECK_APPROACH, $this->arguments[2])) {
//                if ($ndx = array_search(CheckMaximumApproachesReachedMiddleware::class, $this->middlewares)) {
//                    Arr::forget($this->middlewares, $ndx);
//                }
//            }
//        }
        try {
            return parent::dispatchHandlers();
        }
        catch (CaseResolvedException $e) {
            return NextRoute::GO;
        }
        catch (NoTicketException $e) {
            return NextRoute::GO;
        }
        catch (\Exception $e) {
            echo "ConverseAction::dispatchHandlers\n\n\n\n";

            throw $e;
        }
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
}
