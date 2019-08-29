<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\{Redeemed, Listened};
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

    public function onSMSRelayRedeemed(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $contact->notify(new Redeemed($event->getVoucher()));
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            SMSRelayEvents::LISTENED,
            SMSRelayEventSubscriber::class.'@onSMSRelayListened'
        );

        $events->listen(
            SMSRelayEvents::REDEEMED,
            SMSRelayEventSubscriber::class.'@onSMSRelayRedeemed'
        );
    }
}
