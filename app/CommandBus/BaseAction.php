<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Middlewares\LogMiddleware;
use LBHurtado\Missive\Repositories\ContactRepository;
use Joselfonseca\LaravelTactician\CommandBusInterface;

abstract class BaseAction
{
    protected $bus;

    protected $router;

    protected $middlewares;

    protected $contacts;

    protected $permission = 'issue command';

    public function __construct(Router $router, ContactRepository $contacts)
    {
        $this->router = $router;
        $this->contacts = $contacts;
        $this->bus = app(CommandBusInterface::class);
        $this->middlewares = $this->getMiddlewares();
        $this->addBusHandlers();
    }

    abstract protected function addBusHandlers();

    protected function permittedContact($permission = null)
    {
        $contact = $this->router->missive->getContact();

        return $contact->hasPermissionTo($permission ?? $this->permission) ? $contact : null;
    }

    private function getMiddlewares(): array
    {
        return [
          LogMiddleware::class
        ];
    }
}
