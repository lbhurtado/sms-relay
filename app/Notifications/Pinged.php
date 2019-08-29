<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Pinged extends BaseNotification
{
    public function getContent($notifiable)
    {
        return 'PONG';
    }
}
