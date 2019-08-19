<?php

namespace App\CommandBus\Handlers;

use LBHurtado\SMS\Facades\SMS;
use Akaunting\Setting\Facade as Setting;
use App\CommandBus\Commands\ForwardSMSToMobileCommand;

class ForwardSMSToMobileHandler
{
    protected $template = 'relay.forward.mobile';

    /**
     * @param ForwardSMSToMobileCommand $command
     */
    public function handle(ForwardSMSToMobileCommand $command)
    {
        $mobiles = Setting::get('forwarding.mobiles');

        foreach($mobiles as $mobile) {
            SMS::to($mobile)
                ->content($this->getContent($command))
                ->send()
            ;
        }
    }

    protected function getContent(ForwardSMSToMobileCommand $command)
    {
        $from = $command->sms->origin->mobile;
        $message = $command->sms->getMessage();

        return trans($this->template, compact('from','message'));
    }
}
