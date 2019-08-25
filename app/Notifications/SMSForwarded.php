<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class SMSForwarded extends BaseNotification
{
    public function getContent($notifiable)
    {
        return $this->message;
    }
}
