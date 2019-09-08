<?php

namespace App\Listeners;

use App\Events\TicketEvents;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Notifications\{Approach, Endorsed, Updated, Responded};

class TicketEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    public function subscribe($events)
    {
        $events->listen(TicketEvents::OPENED,   Approach::class);
        $events->listen(TicketEvents::ENDORSED, Endorsed::class);
        $events->listen(TicketEvents::UPDATED,  Updated::class);
        $events->listen(TicketEvents::UPDATED,  Responded::class);
    }
}
