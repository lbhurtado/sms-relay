<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Pong extends BaseNotification
{
    public function getContent($notifiable)
    {
        return 'PONG';
    }
}
