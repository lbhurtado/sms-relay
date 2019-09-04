<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Middlewares\LimitMiddleware;
use LBHurtado\Missive\Repositories\ContactRepository;

abstract class LimitingAction extends BaseAction
{
    public function __construct(Router $router, ContactRepository $contacts)
    {
        parent::__construct($router, $contacts);

        $this->addMiddleWare(LimitMiddleware::class);
    }

    abstract protected function addBusHandlers();
}
