<?php

namespace App\CommandBus;

use App\Classes\NextRoute;
use LBHurtado\Missive\Routing\Router;
use App\Exceptions\CaseResolvedException;
use App\CommandBus\Commands\ResolveCommand;
use App\CommandBus\Handlers\ResolveHandler;
use App\CommandBus\Middlewares\{ConverseMiddleware, Statuses};
use LBHurtado\Missive\Repositories\ContactRepository;

class ResolveAction extends BaseAction
{
    protected $permission = 'issue command';

    public function __construct(Router $router, ContactRepository $contacts)
    {
        parent::__construct($router, $contacts);

        $this->addMiddleWare(Statuses::class);
        $this->addMiddleWare(ConverseMiddleware::class);
    }

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        try {
            $this->resolveTicket($data);            
        }
        catch (CaseResolvedException $e) {
            return NextRoute::STOP;
        }
    }

    protected function resolveTicket(array $data)
    {
        $this->bus->dispatch(ResolveCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ResolveCommand::class, ResolveHandler::class);
    }
}
