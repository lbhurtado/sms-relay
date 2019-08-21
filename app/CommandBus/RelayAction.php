<?php

namespace App\CommandBus;

use App\CommandBus\Commands\LogCommand;
use App\CommandBus\Handlers\LogHandler;
use App\CommandBus\Commands\ReplyCommand;
use App\CommandBus\Handlers\ReplyHandler;
use App\CommandBus\Commands\ForwardSMSToMailCommand;
use App\CommandBus\Handlers\ForwardSMSToMailHandler;
use App\CommandBus\Commands\ForwardSMSToMobileCommand;
use App\CommandBus\Handlers\ForwardSMSToMobileHandler;
use App\CommandBus\Commands\ForwardHashtagsToEmailCommand;
use App\CommandBus\Handlers\ForwardHashtagsToEmailHandler;

class RelayAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        if (! $this->permittedContact()) return;

        $go = (object) config('sms-relay.relay');

        $this->log($go->log)
            ->relayToEmail($go->email)
            ->relayToMobile($go->mobile)
            ->reply($go->reply)
            ->relayHashtagsToEmail($go->hashtags);
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(LogCommand::class, LogHandler::class);
        $this->bus->addHandler(ReplyCommand::class, ReplyHandler::class);
        $this->bus->addHandler(ForwardSMSToMailCommand::class, ForwardSMSToMailHandler::class);
        $this->bus->addHandler(ForwardSMSToMobileCommand::class, ForwardSMSToMobileHandler::class);
        $this->bus->addHandler(ForwardHashtagsToEmailCommand::class, ForwardHashtagsToEmailHandler::class);
    }

    protected function log(bool $go = true)
    {
        ! $go || $this->bus->dispatch(LogCommand::class, $this->getData());

        return $this;
    }

    protected function relayToEmail(bool $go = true)
    {
        ! $go ||  $this->bus->dispatch(ForwardSMSToMailCommand::class, $this->getData());

        return $this;
    }

    protected function relayToMobile(bool $go = true)
    {
        ! $go || $this->bus->dispatch(ForwardSMSToMobileCommand::class, $this->getData());

        return $this;
    }

    protected function reply(bool $go = true)
    {
        ! $go || $this->bus->dispatch(ReplyCommand::class, $this->getData());

        return $this;
    }

    protected function relayHashtagsToEmail(bool $go = true)
    {
        ! $go || $this->bus->dispatch(ForwardHashtagsToEmailCommand::class, $this->getData());

        return $this;
    }

    private function getData()
    {
        return [
            'sms' => $this->router->missive->getSMS()
        ];
    }
}
