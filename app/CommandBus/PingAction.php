<?php

namespace App\CommandBus;

use App\Contact;
use App\Traits\HasActionPermissions;
use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\PingCommand;
use App\CommandBus\Handlers\PingHandler;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class PingAction
{
    use HasActionPermissions;

	protected $bus;

    protected $router;

    protected $permission = 'issue command';

	public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->bus->addHandler(PingCommand::class, PingHandler::class);
    }	

    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function ($contact) {
            $this->sendReply(['mobile' => $contact->mobile]); 
        });
    }

    public function sendReply(array $data = [])
    {
        $this->bus->dispatch(PingCommand::class, $data);

        return $this;      
    }
}