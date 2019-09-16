<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use App\CommandBus\Middlewares\CheckCreditsMiddleware;
use LBHurtado\Missive\Repositories\ContactRepository;

abstract class LimitingAction extends BaseAction
{
    public function __construct(Router $router, ContactRepository $contacts)
    {
        parent::__construct($router, $contacts);

        $this->addMiddleWare(CheckCreditsMiddleware::class);
    }

    abstract protected function addBusHandlers();
}
