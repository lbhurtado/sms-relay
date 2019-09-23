<?php

namespace App\CommandBus;

use App\CommandBus\BroadcastAction;
use App\Traits\HasOptionalMiddlewares;
use App\CommandBus\Commands\RelayCommand;
use App\CommandBus\Handlers\RelayHandler;
use App\Exceptions\ShouldBroadcastException;
use App\CommandBus\Middlewares\CheckBroadcasterMiddleware;

class RelayAction extends TemplateAction
{
    use HasOptionalMiddlewares;

    protected $permission = 'send message';

    protected $command = RelayCommand::class;

    protected $handler = RelayHandler::class;

    protected $middlewares = [
        CheckBroadcasterMiddleware::class,
    ];

    public function setup()
    {
        parent::setup();

        $this->addSMSToData()->populateOptionalMiddlewares();
    }

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (ShouldBroadcastException $e) {
            return app(BroadcastAction::class)(...$this->arguments);
        }
        catch (\Exception $e) {
            echo "RelayAction::dispatchHandlers\n\n\n\n";

            throw $e;
        }
    }

    protected function populateOptionalMiddlewares()
    {
        tap((object) config('sms-relay.relay'), function ($go) {
            $this->log($go->log)
                ->email($go->email)
                ->forward($go->mobile)
                ->reply($go->reply)
                ->converse($go->converse)
                ;            
        });

        return $this;
    }
}
