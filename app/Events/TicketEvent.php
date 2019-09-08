<?php

namespace App\Events;

use App\Ticket;
use App\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TicketEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Ticket */
    protected $ticket;

    /** @var Contact */
    protected $responder;

    /** @var string */
    protected $ticket_id;

    /** @var string */
    protected $message;

    /**
     * TicketEvent constructor.
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return Ticket
     */
    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return TicketEvent
     */
    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getOrigin(): Contact
    {
        return $this->getTicket()->contact;
    }

    /**
     * @return Contact
     */
    public function getResponder(): Contact
    {
        return $this->responder;
    }

    /**
     * @param Contact $responder
     * @return TicketEvent
     */
    public function setResponder(Contact $responder): self
    {
        $this->responder = $responder;

        return $this;
    }

    /**
     * @return string
     */
    public function getTicketId(): string
    {
        return $this->ticket_id;
    }

    /**
     * @param string $ticket_id
     * @return TicketEvent
     */
    public function setTicketId(string $ticket_id): self
    {
        $this->ticket_id = $ticket_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return TicketEvent
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
