<?php

namespace App\Listeners;

use App\Notifications\Supported;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\{TicketEvents, TicketEvent};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TicketEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    public function onTicketOpened(TicketEvent $event)
    {
        $ticket = $event->getTicket();
        tap($ticket->contact, function ($contact) use ($event, $ticket) {
            $contact->notify(new Supported($ticket->ticket_id));
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            TicketEvents::OPENED,
            TicketEventSubscriber::class.'@onTicketOpened'
        );
    }
}
