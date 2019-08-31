<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Post extends BaseNotification implements ShouldQueue
{
    public function getContent($notifiable)
    {
        return static::getFormattedMessage($notifiable, $this->message);
    }

    public static function getFormattedMessage($notifiable, $message)
    {
        $handle = $notifiable->handle ?? $notifiable->mobile;
        $signature = config('sms-relay.signature');

        return trans('sms-relay.post', compact('handle', 'message', 'signature'));
    }
}
