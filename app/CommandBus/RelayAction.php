<?php

namespace App\CommandBus;

//use App\CommandBus\Commands\ForwardSMSToMobileCommand;
//use App\CommandBus\Handlers\ForwardSMSToMobileHandler;
use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\CommandBus\Middlewares\{LogMiddleware, EmailMiddleware, ReplyMiddleware};

class RelayAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $this->permittedContact()) return;

        $go = (object) config('sms-relay.relay');

        $this->log($go->log)
            ->email($go->email)
//            ->relayToMobile($go->mobile)
            ->reply($go->reply)
            ->relayHashtagsToEmail($go->hashtags);
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

//    protected function relayToMobile(bool $go = true)
//    {
//        ! $go || $this->bus->dispatch(ForwardSMSToMobileCommand::class, $this->getData(), $this->getMiddlewares());
//
//        return $this;
//    }

    protected function reply(bool $go = true)
    {
        ! $go || $this->addMiddleWare(ReplyMiddleware::class);

        return $this;
    }

    protected function relayHashtagsToEmail(bool $go = true)
    {
        ! $go || $this->bus->dispatch(RelayCommand::class, $this->getData(), $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
//        $this->bus->addHandler(ForwardSMSToMobileCommand::class, ForwardSMSToMobileHandler::class);
        $this->bus->addHandler(RelayCommand::class, RelayHandler::class);
    }

    private function getData()
    {
        return [
            'sms' => $this->router->missive->getSMS()
        ];
    }
}
