<?php

namespace App\CommandBus\Handlers;

use LBHurtado\SMS\Facades\SMS;
use App\CommandBus\Commands\PingCommand;

class PingHandler
{
    protected $message = 'PONG';

    /**
     * @param PingCommand $command
     */
    public function handle(PingCommand $command)
    {
        SMS::from('TXTCMDR')
            ->to($command->mobile)
            ->content($this->message)
            ->send()
        ;        
    }
}
