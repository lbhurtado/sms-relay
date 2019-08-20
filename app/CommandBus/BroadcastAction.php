<?php

namespace App\CommandBus;

use App\Contact;
use App\Traits\HasActionPermissions;
use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\BroadcastCommand;
use App\CommandBus\Handlers\BroadcastHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class BroadcastAction
{
    use HasActionPermissions;

    protected $bus;

    protected $router;

    protected $permission = 'issue command';

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(BroadcastCommand::class, BroadcastHandler::class);
    }

    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function ($contact) use ($values) {
            $this->broadcastMessage($values); 
        });
    }

    public function broadcastMessage(array $data = [])
    {
        $this->bus->dispatch(BroadcastCommand::class, $data);

        return $this;
    }
}
