<?php

namespace App\CommandBus\Handlers;

use LBHurtado\SMS\Facades\SMS;
use App\CommandBus\Commands\SendMessageCommand;

class SendMessageHandler
{
    /**
     * @param SendMessageCommand $command
     */
    public function handle(SendMessageCommand $command)
    {
        SMS::from('TXTCMDR')
        	->to($command->mobile)
        	->content($command->message)
        	->send()
    	;
    }
}
