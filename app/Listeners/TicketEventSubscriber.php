<?php

namespace App\Listeners;

use App\Events\TicketEvents;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Notifications\{Endorse, Approach, Updated, Responded};

class TicketEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    public function subscribe($events)
    {
        $events->listen(TicketEvents::OPENED,   Endorse::class);
        $events->listen(TicketEvents::ENDORSED, Approach::class);
        $events->listen(TicketEvents::UPDATED,  Updated::class);
        $events->listen(TicketEvents::UPDATED,  Responded::class);
    }
}
