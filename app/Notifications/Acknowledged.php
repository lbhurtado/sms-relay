<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Acknowledged extends BaseNotification
{
    public function getContent($notifiable)
    {
        return 'Acknowledged.';
    }
}
