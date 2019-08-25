<?php

namespace App\CommandBus\Handlers;

use App\Jobs\RedeemCodeJob;
use League\Pipeline\Pipeline;
use App\CommandBus\Commands\RedeemCommand;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RedeemHandler
{
    use DispatchesJobs;

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
            $this->dispatch(new RedeemCodeJob($command->origin, $command->code));

            return $command;
        })->pipe(function ($command) {
            tap($command->origin, function ($contact) use ($command) {
                $contact->setEmail($command->email);
            });

            return $command;
        })->process($command);
    }
}
