<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Broadcast extends BaseNotification
{
    public function getContent($notifiable)
    {
        return trim($this->message);
    }
}
