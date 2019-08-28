<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Listened extends BaseNotification implements ShouldQueue
{
    public function getContent($notifiable)
    {
        return $this->message;
    }
}
