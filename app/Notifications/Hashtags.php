<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\Forward;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class Hashtags extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var SMS */
    protected $sms;

    /**
     * MailHashtags constructor.
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
        return ['mail', 'database']; //TODO use SEND_NOTIFICATION in .env
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \App\Mail\Forward
     */
    public function toMail($notifiable)
    {
        return (new Forward($notifiable, $this->sms));
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
            'sms' => $this->sms
        ];
    }
}
