<?php

namespace App\Notifications;

use App\Contact;
use App\Events\TicketEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use LBHurtado\EngageSpark\Notifications\BaseNotification;

class Approach extends BaseNotification implements ShouldQueue
{
    public function getContent($notifiable)
    {
        return static::getFormattedMessage($notifiable, $this->message);
    }

    public static function getFormattedMessage($notifiable, $message)
    {
        $handle = $notifiable->handle ?? $notifiable->mobile;
        $signature = config('sms-relay.signature');

        return trans('sms-relay.approach', compact('handle', 'message', 'signature'));
    }

    public function handle(TicketEvent $event)
    {
        $this->sendEndorsements();

        $event->getTicket()->endorse();
    }

    protected function sendEndorsements(): void
    {
        tap(app(\Illuminate\Contracts\Notifications\Dispatcher::class), function ($dispatcher) {
            Contact::role('supporter')->each(function ($contact) use ($dispatcher) {
                $dispatcher->sendNow($contact, $this);
            });

        });
    }
}
