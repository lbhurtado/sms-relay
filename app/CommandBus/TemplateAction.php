<?php

namespace App\CommandBus;

use Illuminate\Support\Arr;
use LBHurtado\Missive\Routing\Router;
use LBHurtado\Tactician\Classes\ActionAbstract;
use LBHurtado\Tactician\Contracts\ActionInterface;

class TemplateAction extends ActionAbstract implements ActionInterface
{
    /** @var Router */
    protected $router;

    /** @var string */
    protected $permission = 'issue command';

    /** @var array */
    protected $middlewares = [];

    /** @var array */
    protected $data = [];
    /**
     * TemplateAction constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        parent::__construct(
            app(\Joselfonseca\LaravelTactician\CommandBusInterface::class),
            app(\Opis\Events\EventDispatcher::class),
            app(\Illuminate\Http\Request::class)
        );

        $this->router = $router;
    }

    /**
     * Add ['origin' => $origin] to $this->data for all actions
     */
    public function setup()
    {
        $this->addOriginToData()->addArgumentsToData();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function addArgumentsToData()
    {
        $params = Arr::get($this->arguments, 1);
        $this->data = array_merge($this->data, $params);

        return $this;
    }

    /**
     * @param null $permission
     * @return \LBHurtado\Missive\Models\Contact|null
     */
    protected function permittedContact($permission = null)
    {
        $contact = $this->router->missive->getContact();

        return $contact->hasPermissionTo($permission ?? $this->permission) ? $contact : null;
    }

    /**
     * @return $this
     */
    protected function addOriginToData()
    {
        $origin = $this->permittedContact();
        $this->data = array_merge($this->data, compact('origin'));

        return $this;
    }

    /**
     * @return \LBHurtado\Missive\Classes\SMSAbstract
     */
    protected function getSMS()
    {
        return $this->router->missive->getSMS();
    }
}
