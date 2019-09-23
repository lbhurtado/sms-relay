<?php

namespace App\CommandBus;

use App\CommandBus\BroadcastAction;
use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\Exceptions\ShouldBroadcastException;
use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;
use App\CommandBus\Middlewares\{
    LogMiddleware, 
    EmailMiddleware, 
    ReplyMiddleware, 
    ForwardMiddleware, 
    RecordDiscussionMiddleware,
    CheckBroadcasterMiddleware,
};

class RelayAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = RelayCommand::class;

    protected $handler = RelayHandler::class;

    protected $middlewares = [
        CheckBroadcasterMiddleware::class,
    ];

    public function setup()
    {
        parent::setup();

        $this->addSMSToData()->populateMiddlewares();
    }

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (ShouldBroadcastException $e) {
            return app(BroadcastAction::class)(...$this->arguments);
        }
        catch (\Exception $e) {
            echo "RelayAction::dispatchHandlers\n\n\n\n";

            throw $e;
        }
    }

    protected function addSMSToData()
    {
        $sms = $this->getSMS();
        $this->data = array_merge($this->data, compact('sms'));

        return $this;
    }

    protected function populateMiddlewares()
    {
        tap((object) config('sms-relay.relay'), function ($go) {
            $this->log($go->log)
                ->email($go->email)
                ->forward($go->mobile)
                ->reply($go->reply)
                ->converse($go->converse)
                ;            
        });

        return $this;
    }

    protected function log(bool $go = true)
    {
        ! $go || $this->addMiddleWare(LogMiddleware::class);

        return $this;
    }

    protected function email(bool $go = true)
    {
        ! $go || $this->addMiddleWare(EmailMiddleware::class);

        return $this;
    }

    protected function forward(bool $go = true)
    {
        ! $go || $this->addMiddleWare(ForwardMiddleware::class);

        return $this;
    }

    protected function reply(bool $go = true)
    {
        ! $go || $this->addMiddleWare(ReplyMiddleware::class);

        return $this;
    }

    protected function converse($go = true)
    {
        $this->addMiddleWare(RecordDiscussionMiddleware::class);

        return $this;
    }
}
