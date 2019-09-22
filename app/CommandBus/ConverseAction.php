<?php

namespace App\CommandBus;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use App\Classes\{NextRoute, Hash};
use App\CommandBus\Commands\ConverseCommand;
use App\CommandBus\Handlers\ConverseHandler;
use App\Exceptions\{CaseResolvedException, NoTicketException};
use App\CommandBus\Middlewares\{CheckNoTicketMiddleware, CheckCaseResolvedMiddleware, RecordDiscussionMiddleware};

class ConverseAction extends TemplateAction
{
    protected $permission = 'send message';

    protected $command = ConverseCommand::class;

    protected $handler = ConverseHandler::class;

    protected $middlewares = [
        CheckNoTicketMiddleware::class,
        CheckCaseResolvedMiddleware::class,
        RecordDiscussionMiddleware::class
    ];

    public function dispatchHandlers()
    {
        try {
            return parent::dispatchHandlers();
        }
        catch (CaseResolvedException $e) {
            return NextRoute::GO;
        }
        catch (NoTicketException $e) {
            return NextRoute::GO;
        }
        catch (InvalidArgumentException $e) {
            $updatedArguments = $this->updateArguments($this->arguments);

            return app(static::class)(...$updatedArguments);
        }
        catch (\Exception $e) {
            echo "ConverseAction::dispatchHandlers\n\n\n\n";

            throw $e;
        }
    }

    protected function updateArguments(array $arguments): array
    {
        $parameters = [
            'msg' => $this->getMsg($arguments),
            'ticket_id' => $this->getHash()
        ];
        Arr::set($arguments, 1, $parameters);

        return $arguments;
    }

    protected function getMsg($arguments): string
    {
        return Arr::get($arguments, '1.msg') ?? Arr::get($arguments, '1.message');
    }

    protected function getHash(): string
    {
        return optional($this->data['origin']->tickets->last())->ticket_id ?? Hash::EMPTY;
    }
}
