<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\ForwardSMSToMail;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailHashtags extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \App\Mail\ForwardSMSToMail
     */
    public function toMail($notifiable)
    {
        return (new ForwardSMSToMail($notifiable, $this->sms));
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
