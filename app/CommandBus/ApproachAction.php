<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Commands\{ApproachCommand, RespondCommand};
use App\CommandBus\Handlers\{ApproachHandler, RespondHandler};
use App\CommandBus\Middlewares\ConfineMiddleware;
use LBHurtado\Missive\Repositories\ContactRepository;

use App\CommandBus\Middlewares\AttachSMSMiddleware;

class ApproachAction extends BaseAction
{
    protected $permission = 'send message';

    public function __construct(Router $router, ContactRepository $contacts)
    {
        parent::__construct($router, $contacts);

        $this->addMiddleWare(ConfineMiddleware::class);
        $this->addMiddleWare(AttachSMSMiddleware::class);
    }

    public function __invoke(string $path, array $values)
    {
        if (! $origin = $this->permittedContact()) return;

        $data = array_merge($values, compact('origin'));

        $this->approach($data);
    }

    protected function approach(array $data)
    {
        $this->bus->dispatch(ApproachCommand::class, $data, $this->getMiddlewares());
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(ApproachCommand::class, ApproachHandler::class);
        $this->bus->addHandler(RespondCommand::class, RespondHandler::class);
    }
}
