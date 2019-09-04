<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Voucher extends BaseNotification
{
    public function getContent($notifiable)
    {
        return 'VOUCHER'; //TODO finish this, baka puwede sa markdown view
    }
}
