<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use Joselfonseca\LaravelTactician\CommandBusInterface;

abstract class BaseAction
{
    protected $bus;

    protected $router;

    protected $permission = 'issue command';

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->bus = app(CommandBusInterface::class);
        $this->addBusHandlers();
    }

    abstract protected function addBusHandlers();

    protected function permittedContact($permission = null)
    {
        $contact = $this->router->missive->getContact();

        return $contact->hasPermissionTo($permission ?? $this->permission) ? $contact : null;
    }
}
