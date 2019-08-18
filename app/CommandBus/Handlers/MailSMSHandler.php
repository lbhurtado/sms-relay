<?php

namespace App\CommandBus\Handlers;

use App\Mail\SMSForward;
use Illuminate\Support\Facades\Mail;
use Akaunting\Setting\Facade as Setting;
use App\CommandBus\Commands\MailSMSCommand;

class MailSMSHandler
{
    /**
     * @param MailSMSCommand $command
     */
    public function handle(MailSMSCommand $command)
    {
        $emails = Setting::get('forwarding.emails');

        foreach($emails as $email) {
            Mail::to($email)
                ->send(new SMSForward($command->sms))
            ;
        }

    }
}
