<?php

namespace App\CommandBus\Handlers;

use App\Contact;
use LBHurtado\EngageSpark\Notifications\Adhoc;
use Akaunting\Setting\Facade as Setting;
use App\CommandBus\Commands\ForwardSMSToMobileCommand;

class ForwardSMSToMobileHandler
{
    protected $template = 'sms-relay.forward.mobile';

    /**
     * @param ForwardSMSToMobileCommand $command
     */
    public function handle(ForwardSMSToMobileCommand $command)
    {
        $mobiles = Setting::get('forwarding.mobiles');

        foreach($mobiles as $mobile) {
            tap($this->getContact($mobile), function($contact) use ($command) {
                $contact->notify(new Adhoc($this->getContent($command)));
            });
        }
    }

    protected function getContent(ForwardSMSToMobileCommand $command)
    {
        $from = $command->sms->origin->mobile;
        $to = $command->sms->destination->mobile;
        $message = $command->sms->getMessage();

        return trans($this->template, compact('from', 'to', 'message'));
    }

    protected function getContact(string $mobile)
    {
        $mobile = phone($mobile, 'PH')->formatE164();

        return Contact::firstOrCreate(compact('mobile'));
    }
}
