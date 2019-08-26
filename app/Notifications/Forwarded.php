<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Forwarded extends BaseNotification
{
    public function getContent($notifiable)
    {
        return $this->message;
    }
}
