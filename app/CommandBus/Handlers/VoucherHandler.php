<?php

namespace App\CommandBus\Handlers;

use Setting;
use App\Notifications\Voucher;
use App\CommandBus\Commands\VoucherCommand;

class VoucherHandler
{
    /**
     * @param VoucherCommand $command
     */
    public function handle(VoucherCommand $command)
    {
        if ($command->pin == Setting::get('PIN')) {
            $command->origin->notify(new Voucher);
        }
    }
}
