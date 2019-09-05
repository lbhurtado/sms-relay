<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;
use App\CommandBus\Middlewares\{LogMiddleware, EmailMiddleware, ReplyMiddleware, ForwardMiddleware};

class RelayAction extends BaseAction
{
    protected $permission = 'send message';

    protected $broadcastData = [];

    protected $values = [];

    public function __invoke(string $path, array $values)
    {
        if (! tap($this->permittedContact(), function ($origin) use ($values) {
            if ($origin->hasPermissionTo('send broadcast')) {
                $this->broadcastData = array_merge($values, compact('origin'));
            }
        })) return;

        $go = $this->shouldProceed();

        $this->log($go->log)
            ->email($go->email)
            ->forward($go->mobile)
            ->reply($go->reply)
            ->relay($go->hashtags && ! $this->shouldBroadcast())
            ->broadcast($this->shouldBroadcast())
            ;
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

    protected function relay(bool $go = true)
    {
        ! $go || $this->bus->dispatch(RelayCommand::class, $this->getData(), $this->getMiddlewares());

        return $this;
    }

    protected function broadcast(bool $go = true)
    {
        ! $go ||  $this->bus->dispatch(BroadcastCommand::class, $this->broadcastData, $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RelayCommand::class, RelayHandler::class);
        $this->bus->addHandler(BroadcastCommand::class, BroadcastHandler::class);
    }

    private function getData()
    {
        return [
            'sms' => $this->router->missive->getSMS(),
        ];
    }

    protected function shouldProceed()
    {
        return (object) config('sms-relay.relay');
    }

    protected function shouldBroadcast()
    {
        return (bool) config('sms-relay.broadcast.optional') && (bool) $this->broadcastData;
    }
}
