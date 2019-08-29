<?php

namespace App\CommandBus;

use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\CommandBus\Middlewares\{LogMiddleware, EmailMiddleware, ReplyMiddleware, ForwardMiddleware};

class RelayAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $this->permittedContact()) return;

        $go = (object) config('sms-relay.relay');

        $this->log($go->log)
            ->email($go->email)
            ->forward($go->mobile)
            ->reply($go->reply)
            ->relay($go->hashtags);
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

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RelayCommand::class, RelayHandler::class);
    }

    private function getData()
    {
        return [
            'sms' => $this->router->missive->getSMS()
        ];
    }
}
