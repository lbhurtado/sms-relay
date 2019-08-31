<?php

namespace App\CommandBus;

use App\CommandBus\Commands\PostCommand;
use App\CommandBus\Handlers\PostHandler;

class PostAction extends BaseAction
{
    protected $permission = 'send broadcast';

    public function __invoke(string $path, array $values)
    {
        optional($this->permittedContact(), function($origin) use ($values) {
            $this->postMessage(array_merge($values, compact('origin')));
        });
    }

    /**
     * @param array $data
     * e.g. $data = ['message' => 'The #quick brown fox...']
     * @return $this
     */
    public function postMessage(array $data = [])
    {
        $this->bus->dispatch(PostCommand::class, $data, $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(PostCommand::class, PostHandler::class);
    }
}
