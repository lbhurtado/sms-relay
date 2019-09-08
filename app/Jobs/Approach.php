<?php

namespace App\Jobs;

use App\{Contact, Ticket};
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Approach implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Contact */
    public $origin;

    /** @var string */
    public $message;

    /**
     * Approach constructor.
     * @param Contact $origin
     * @param string $message
     */
    public function __construct(Contact $origin, string $message)
    {
        $this->origin = $origin;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Ticket::open($this->origin, $this->message);
    }
}
