<?php

namespace App\CommandBus;

use App\Traits\HasActionPermissions;
use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\LogCommand;
use App\CommandBus\Handlers\LogHandler;
use App\CommandBus\Commands\ReplyCommand;
use App\CommandBus\Handlers\ReplyHandler;
use App\CommandBus\Commands\ProcessHashtagsCommand;
use App\CommandBus\Handlers\ProcessHashtagsHandler;
use App\CommandBus\Commands\ForwardSMSToMailCommand;
use App\CommandBus\Handlers\ForwardSMSToMailHandler;
use App\CommandBus\Commands\ForwardSMSToMobileCommand;
use App\CommandBus\Handlers\ForwardSMSToMobileHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class RelayAction
{
    use HasActionPermissions;

    protected $bus;

    protected $router;

    protected $permission = 'send message';

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->addBusHandlers();
    }

    public function __invoke(string $path, array $values)
    {
        if (! $this->permittedContact()) return;

        $go = (object) config('sms-relay.relay');

        $this->log($go->log)
            ->relayToEmail($go->email)
            ->relayToMobile($go->mobile)
            ->reply($go->reply)
            ->processMessage(true); 
    }

    protected function addBusHandlers()
    {
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(LogCommand::class, LogHandler::class);
        $this->bus->addHandler(ReplyCommand::class, ReplyHandler::class);
        $this->bus->addHandler(ForwardSMSToMailCommand::class, ForwardSMSToMailHandler::class);
        $this->bus->addHandler(ForwardSMSToMobileCommand::class, ForwardSMSToMobileHandler::class);

        $this->bus->addHandler(ProcessHashtagsCommand::class, ProcessHashtagsHandler::class);
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

    protected function processMessage(bool $go = true)
    {
        $this->bus->dispatch(ProcessHashtagsCommand::class, $this->getData());

        return $this;
    }

    private function getData()
    {
        return [
            'sms' => $this->router->missive->getSMS()
        ];
    }
}
