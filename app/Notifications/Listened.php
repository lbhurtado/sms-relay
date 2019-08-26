<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Listened extends BaseNotification
{
    public function getContent($notifiable)
    {
        return $this->message;
    }
}
