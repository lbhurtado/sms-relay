<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;
use App\CommandBus\Middlewares\{LogMiddleware, EmailMiddleware, ReplyMiddleware, ForwardMiddleware, RecordDiscussionMiddleware};

class RelayAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = RelayCommand::class;

    protected $handler = RelayHandler::class;

    public function setup()
    {
        parent::setup();

        $this->addSMSToData();

        $go = $this->shouldProceed();

        $this->log($go->log)
            ->email($go->email)
            ->forward($go->mobile)
            ->reply($go->reply)
            ->converse($go->converse)
//            ->relay($go->hashtags && ! $this->shouldBroadcast())
//            ->broadcast($this->shouldBroadcast())
            ;
    }

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (\Exception $e) {
            echo "RelayAction::dispatchHandlers\n\n\n\n";

//            throw $e;
        }
    }

    /**
     * @return $this
     */
    protected function addSMSToData()
    {
        $sms = $this->getSMS();
        $this->data = array_merge($this->data, compact('sms'));

        return $this;
    }
//    protected $broadcastData = [];
//
//    protected $values = [];
//
//    public function __invoke(string $path, array $values)
//    {
//        if (! tap($this->permittedContact(), function ($origin) use ($values) {
//            if ($origin->hasPermissionTo('send broadcast')) {
//                $this->broadcastData = array_merge($values, compact('origin'));
//            }
//        })) return;
//
//        dd($values);
//        dd($this->broadcastData);
//        $go = $this->shouldProceed();
//
//        $this->log($go->log)
//            ->email($go->email)
//            ->forward($go->mobile)
//            ->reply($go->reply)
//            ->converse($go->converse)
//            ->relay($go->hashtags && ! $this->shouldBroadcast())
//            ->broadcast($this->shouldBroadcast())
//            ;
//    }
//
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

//    protected function relay(bool $go = true)
//    {
//        ! $go || $this->bus->dispatch(RelayCommand::class, $this->getData(), $this->getMiddlewares());
//
//        return $this;
//    }
//
//    protected function broadcast(bool $go = true)
//    {
//        ! $go ||  $this->bus->dispatch(BroadcastCommand::class, $this->broadcastData, $this->getMiddlewares());
//
//        return $this;
//    }
//
    protected function converse($go = true)
    {
        $this->addMiddleWare(RecordDiscussionMiddleware::class);

        return $this;
    }
//
//    protected function addBusHandlers()
//    {
//        $this->bus->addHandler(RelayCommand::class, RelayHandler::class);
//        $this->bus->addHandler(BroadcastCommand::class, BroadcastHandler::class);
//    }
//
//    private function getData()
//    {
//        return [
//            'sms' => $this->router->missive->getSMS(),
//        ];
//    }
//

    protected function addMiddleWare(string $middleware)
    {
        if (! in_array($middleware, $this->middlewares))
            array_push($this->middlewares, $middleware);

        return $this;
    }

    protected function shouldProceed()
    {
        return (object) config('sms-relay.relay');
    }

//    protected function shouldBroadcast()
//    {
//        return (bool) config('sms-relay.broadcast.optional') && (bool) $this->broadcastData;
//    }
}
