<?php

namespace App\Jobs;

use App\Ticket as Ticketing;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Ticket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Contact */
    public $contact;

    /** @var string */
    public $title;

    /** @var string */
    public $message;

    /**
     * RedeemCode constructor.
     * @param \App\Contact $contact
     * @param string $title
     * @param string $message
     */
    public function __construct(\App\Contact $contact, string $title, string $message)
    {
        $this->contact = $contact;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Ticketing::generate($this->contact, $this->title, $this->message);
    }
}
