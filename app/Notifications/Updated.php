<?php

namespace App\Notifications;

use App\Contact;
use App\Events\TicketEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Updated extends BaseNotification implements ShouldQueue
{
    public function getContent($notifiable)
    {
        return static::getFormattedMessage($notifiable, $this->message);
    }

    public static function getFormattedMessage($notifiable, $message)
    {
        $handle = $notifiable->handle ?? $notifiable->mobile;
        $signature = config('sms-relay.signature');

        return trans('sms-relay.update', compact('handle', 'message', 'signature'));
    }

    public function handle(TicketEvent $event)
    {
        $this->sendUpdate($event->getResponder());
    }

    protected function sendUpdate(Contact $contact): void
    {
        tap(app(\Illuminate\Contracts\Notifications\Dispatcher::class), function ($dispatcher) use ($contact) {
            $dispatcher->sendNow($contact, $this);
        });
    }
}
