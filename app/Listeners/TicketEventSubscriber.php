<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Events\{TicketEvents, TicketEvent};
use App\Notifications\{Supported, Updated, Responded};
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

    public function onTicketUpdated(TicketEvent $event)
    {
        $ticket = $event->getTicket();
        tap($ticket->contact, function ($contact) use ($event, $ticket) {
            $contact->notify(new Responded($ticket->latestStatus('update')->reason));
        });
        tap($event->getResponder(), function ($contact) {
            $contact->notify( new Updated('asdds'));
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            TicketEvents::OPENED,
            TicketEventSubscriber::class.'@onTicketOpened'
        );

        $events->listen(
            TicketEvents::UPDATED,
            TicketEventSubscriber::class.'@onTicketUpdated'
        );
    }
}
