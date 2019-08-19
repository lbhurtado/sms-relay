<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use League\Pipeline\Pipeline;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class ForwardSMSToMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mobile;

    public $message;

    public function __construct(SMS $sms)
    {
        $this->mobile = $sms->origin->mobile;
        $this->message = $sms->getMessage();
    }

    public function build()
    {
        return $this->subject('Forward SMS To Mail')
            ->markdown('email.forward')
            ->with([
                'name' => 'New Mailtrap User',
                'link' => 'https://mailtrap.io/inboxes'
            ]);
    }
}
