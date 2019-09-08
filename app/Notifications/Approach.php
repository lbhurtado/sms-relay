<?php

namespace App\Notifications;

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

        return trans('sms-relay.support', compact('handle', 'message', 'signature'));
    }

    public function handle(TicketEvent $event)
    {
        $this->sendApproach($event);

        $event->getTicket()->approach();
    }

    /**
     * @param TicketEvent $event
     */
    protected function sendApproach(TicketEvent $event): void
    {
        tap(app(\Illuminate\Contracts\Notifications\Dispatcher::class), function ($dispatcher) use ($event) {
            $dispatcher->sendNow($event->getOrigin(), $this);
        });
    }
}
