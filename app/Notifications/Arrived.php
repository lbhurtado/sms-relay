<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Arrived extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var SMS */
    protected $sms;

    /**
     * SMSArrived constructor.
     * @param SMS $sms
     */
    public function __construct(SMS $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; //TODO database not allowed yet- check this out in the future
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('sms.arrived', ['sms' => $this->sms]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
