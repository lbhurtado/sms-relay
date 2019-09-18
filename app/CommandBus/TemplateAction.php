<?php

namespace App\CommandBus;

use LBHurtado\Missive\Routing\Router;
use LBHurtado\Tactician\Classes\ActionAbstract;
use LBHurtado\Tactician\Contracts\ActionInterface;

class TemplateAction extends ActionAbstract implements ActionInterface
{
    protected $router;

    protected $permission = 'issue command';

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    protected function permittedContact($permission = null)
    {
        $contact = $this->router->missive->getContact();

        return $contact->hasPermissionTo($permission ?? $this->permission) ? $contact : null;
    }

    protected function addOrigin(array $data)
    {
        $origin = $this->permittedContact();

        return array_merge($data, compact('origin'));
    }

    protected function getSMS()
    {
        return $this->router->missive->getSMS();
    }
}
