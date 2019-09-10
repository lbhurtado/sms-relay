<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\RespondCommand;
use App\CommandBus\Handlers\RespondHandler;
use App\CommandBus\Middlewares\ConverseMiddleware;
use LBHurtado\Missive\Repositories\ContactRepository;

class RespondAction extends BaseAction
{
    protected $permission = 'issue command';

    public function __construct(Router $router, ContactRepository $contacts)
    {
        parent::__construct($router, $contacts);

        $this->addMiddleWare(ConverseMiddleware::class);
    }

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->updateTicket($data);
    }

    protected function updateTicket(array $data)
    {
        $this->bus->dispatch(RespondCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(RespondCommand::class, RespondHandler::class);
    }
}
