<?php

namespace App\Events;

use App\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SMSRelayEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Contact */
    protected $contact;

    /** @var array */
    protected $hashtags;

    /** @var Voucher */
    protected $voucher;

    /**
     * SMSRelayEvent constructor.
     * @param Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return Voucher
     */
    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    /**
     * @param Voucher $voucher
     * @return SMSRelayEvent
     */
    public function setVoucher(Voucher $voucher): SMSRelayEvent
    {
        $this->voucher = $voucher;

        return $this;
    }

    /**
     * @return array
     */
    public function getHashtags(): array
    {
        return $this->hashtags;
    }

    /**
     * @param array $hashtags
     * @return SMSRelayEvent
     */
    public function setHashtags(array $hashtags): SMSRelayEvent
    {
        $this->hashtags = $hashtags;

        return $this;
    }

    /**
     * @return string
     */
    public function getTags(): string
    {
        return implode(' ', $this->getHashtags());
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
