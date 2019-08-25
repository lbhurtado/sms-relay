<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class SMSAcknowledged extends BaseNotification
{
    public function getContent($notifiable)
    {
        return 'Acknowledged.';
    }
}
