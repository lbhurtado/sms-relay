<?php

namespace App\Listeners;

use App\Jobs\Credit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Events\{SMSRelayEvent, SMSRelayEvents};
use App\Notifications\{Redeemed, Listened, Relayed, Unlistened};

class SMSRelayEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    public function onSMSRelayListened(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $contact->notify(new Listened($event->getTags()));
        });
    }

    public function onSMSRelayRedeemed(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $this->dispatch(new Credit($contact, config('sms-relay.credits.initial.spokesman')));
            $contact->notify(new Redeemed($event->getVoucher()));
        });
    }

    public function onSMSRelayRelayed(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $contact->notify(new Relayed($event->getMessage()));
        });
    }

    public function onSMSRelayUnlistened(SMSRelayEvent $event)
    {
        tap($event->getContact(), function ($contact) use ($event) {
            $contact->notify(new Unlistened($event->getTags()));
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

        $events->listen(
            SMSRelayEvents::RELAYED,
            SMSRelayEventSubscriber::class.'@onSMSRelayRelayed'
        );
        $events->listen(
            SMSRelayEvents::UNLISTENED,
            SMSRelayEventSubscriber::class.'@onSMSRelayUnlistened'
        );
    }
}
