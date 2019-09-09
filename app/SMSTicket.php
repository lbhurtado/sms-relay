<?php

namespace App;

use App\Ticket;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SMSTicket extends Pivot
{
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
