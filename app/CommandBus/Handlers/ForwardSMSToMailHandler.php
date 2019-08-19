<?php

namespace App\CommandBus\Handlers;

use App\Mail\ForwardSMSToMail;
use Illuminate\Support\Facades\Mail;
use Akaunting\Setting\Facade as Setting;
use App\CommandBus\Commands\ForwardSMSToMailCommand;

class ForwardSMSToMailHandler
{
    /**
     * @param ForwardSMSToMailCommand $command
     */
    public function handle(ForwardSMSToMailCommand $command)
    {
        $emails = Setting::get('forwarding.emails');

        foreach($emails as $email) {
            Mail::to($email)
                ->send(new ForwardSMSToMail($command->sms))
            ;
        }
    }
}
