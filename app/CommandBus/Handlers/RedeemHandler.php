<?php

namespace App\CommandBus\Handlers;

use League\Pipeline\Pipeline;
use App\CommandBus\Commands\RedeemCommand;

class RedeemHandler
{
    /**
     * @var \BeyondCode\Vouchers\Models\Voucher
     */
    protected $voucher;

    /**
     * @param RedeemCommand $command
     */
    public function handle(RedeemCommand $command)
    {
        (new Pipeline)->pipe(function ($command) {
            tap($command->origin, function ($contact) use ($command) {
                tap($command->code, function ($code) use ($contact) {
                    $this->voucher = $contact->redeemCode($code);
                });
            });

            return $command;
        })->pipe(function ($command) {
            tap($command->origin, function ($contact) {
                tap($this->voucher->model, function ($role) use ($contact) {
                    $contact->syncRoles($role);
                });
            });

            return $command;
        })->pipe(function ($command) {
            tap($command->origin, function ($contact) use ($command) {
                $contact->setEmail($command->email);
            });

            return $command;
        })->process($command);
    }
}
