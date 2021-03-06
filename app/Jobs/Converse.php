<?php

namespace App\Jobs;

use App\{Contact, Ticket};
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Converse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Contact */
    public $origin;

    /** @var string */
    public $ticket_id;

    /** @var string */
    public $message;

    /**
     * Converse constructor.
     * @param Contact $origin
     * @param string $ticket_id
     * @param string $message
     */
    public function __construct(Contact $origin, string $ticket_id, string $message)
    {
        $this->origin = $origin;
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
        Ticket::fromHash($this->ticket_id)->converse($this->origin, $this->message);
    }
}
