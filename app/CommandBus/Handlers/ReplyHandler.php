<?php

namespace App\CommandBus\Handlers;

use LBHurtado\SMS\Facades\SMS;
use App\CommandBus\Commands\ReplyCommand;

class ReplyHandler
{
    protected $template = 'sms-relay.reply.standard';

    /**
     * @param ReplyCommand $command
     */
    public function handle(ReplyCommand $command)
    {
        $mobile = $command->sms->origin->mobile;

        SMS::to($mobile)
            ->content($this->getContent($command))
            ->send()
        ;
    }

    protected function getContent(ReplyCommand $command)
    {
        $from = $command->sms->origin->mobile;
        $message = $command->sms->getMessage();

        return trans($this->template, compact('from','message'));
    }
}
