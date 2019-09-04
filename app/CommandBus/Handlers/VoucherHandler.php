<?php

namespace App\CommandBus\Handlers;

use Setting;
use App\Notifications\Voucher;
use App\CommandBus\Commands\VoucherCommand;
use BeyondCode\Vouchers\Models\Voucher as Vouchers;

class VoucherHandler
{
    /**
     * @param VoucherCommand $command
     */
    public function handle(VoucherCommand $command)
    {
        if ($command->pin == Setting::get('PIN')) {
            $command->origin->notify(new Voucher($this->getMessage()));
        }
    }

    protected function getMessage()
    {
        $text = "\n";

        Vouchers::all()->map(function($voucher){return $voucher->code . ' ' . $voucher->model->name ;})
            ->each(function ($items) use (&$text) {
                $text .= $items . "\n";
            });

        return $text;
    }
}
