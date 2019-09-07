<?php

namespace App\Jobs;

use App\{Contact, Ticket};
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Respond implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Contact */
    public $contact;

    /** @var string */
    public $ticket_id;

    /** @var string */
    public $message;

    /**
     * RedeemCode constructor.
     * @param Contact $contact
     * @param string $ticket_id
     * @param string $message
     */
    public function __construct(Contact $contact, string $ticket_id, string $message)
    {
        $this->contact = $contact;
        $this->ticket_id = $ticket_id;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Ticket::respond($this->contact, $this->ticket_id, $this->message);
    }
}
