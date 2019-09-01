<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Credit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Contact */
    public $contact;

    /** @var int */
    public $amount;

    /**
     * Credit constructor.
     * @param \App\Contact $contact
     * @param int $amount
     */
    public function __construct(\App\Contact $contact, int $amount)
    {
        $this->contact = $contact;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->contact->increase($this->amount);
    }
}
