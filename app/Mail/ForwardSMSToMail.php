<?php

namespace App\Mail;

use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForwardSMSToMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contact;

    public $sms;

    public function __construct(Contact $contact, SMS $sms)
    {
        $this->contact = $contact;
        $this->sms = $sms;
    }

    public function build()
    {
        return $this->subject('Forward SMS To Mail')
            ->to($this->contact->email)
            ->markdown('email.forward')
            ->with([
                'name' => 'New Mailtrap User',
                'link' => 'https://mailtrap.io/inboxes'
            ]);
    }
}
