<?php

namespace App\CommandBus\Handlers;

use App\CommandBus\Commands\VoucherCommand;

/**
 * Class VoucherHandler
 * @package App\CommandBus\Handlers
 */
class VoucherHandler
{
    /**
     * VoucherHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param VoucherCommand $command
     */
    public function handle(VoucherCommand $command)
    {
    }
}
