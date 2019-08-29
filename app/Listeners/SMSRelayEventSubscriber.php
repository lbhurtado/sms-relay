<?php

namespace App\Listeners;

use App\Notifications\Listened;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\{SMSRelayEvent, SMSRelayEvents};

class SMSRelayEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    public function onSMSRelayListened(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $contact->notify(new Listened($event->getTags()));
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSRelayEvents::LISTENED,
            SMSRelayEventSubscriber::class.'@onSMSRelayListened'
        );
    }
}
