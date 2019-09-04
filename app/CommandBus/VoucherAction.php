<?php

namespace App\CommandBus;

use App\Contact;
use Illuminate\Support\Arr;
use App\CommandBus\Commands\VoucherCommand;
use App\CommandBus\Handlers\VoucherHandler;

class VoucherAction extends BaseAction
{
    protected $permission = 'send message';

    public function __invoke(string $path, array $values)
    {
        $pin = Arr::get($values, 'pin');
        optional($this->permittedContact(), function ($contact) use ($pin) {
            $this->sendReply($contact, $pin);
        });
    }

    public function sendReply(Contact $origin, int $pin)
    {
        $data = compact('origin', 'pin');
        $this->bus->dispatch(VoucherCommand::class, $data, $this->getMiddlewares());

        return $this;
    }

    protected function addBusHandlers()
    {
        $this->bus->addHandler(VoucherCommand::class, VoucherHandler::class);
    }
}
