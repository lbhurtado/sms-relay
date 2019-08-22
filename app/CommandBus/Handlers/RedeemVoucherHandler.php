<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\RedeemVoucherCommand;
use League\Pipeline\Pipeline;

class RedeemVoucherHandler
{
    /**
     * @var \BeyondCode\Vouchers\Models\Voucher
     */
    protected $voucher;

    /**
     * @param RedeemVoucherCommand $command
     */
    public function handle(RedeemVoucherCommand $command)
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
                $contact->extra_attributes['email'] = $command->email;
            })->save();

            return $command;
        })->process($command);
    }
}
