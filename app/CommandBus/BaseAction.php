<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use LBHurtado\Missive\Repositories\ContactRepository;
use Joselfonseca\LaravelTactician\CommandBusInterface;

abstract class BaseAction
{
    protected $bus;

    protected $router;

    protected $contacts;

    protected $permission = 'issue command';

    private $middlewares = [];

    public function __construct(Router $router, ContactRepository $contacts)
    {
        $this->router = $router;
        $this->contacts = $contacts;
        $this->bus = app(CommandBusInterface::class);
        $this->addBusHandlers();
    }

    abstract protected function addBusHandlers();

    protected function permittedContact($permission = null)
    {
        $contact = $this->router->missive->getContact();

        return $contact->hasPermissionTo($permission ?? $this->permission) ? $contact : null;
    }

    protected function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    protected function addMiddleWare(string $middleware)
    {
        if (! in_array($middleware, $this->middlewares))
            array_push($this->middlewares, $middleware);

        return $this;
    }

    protected function addOrigin(array $data)
    {
        $origin = $this->permittedContact();

        return array_merge($data, compact('origin'));
    }
}
