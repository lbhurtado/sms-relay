<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Unlisten implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Contact */
    public $contact;

    /** @var string */
    public $tags;

    /**
     * Listen constructor.
     * @param \App\Contact $contact
     * @param string $tags
     */
    public function __construct(\App\Contact $contact, string $tags)
    {
        $this->contact = $contact;
        $this->tags = $tags;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->contact->uncatch($this->getHashtags($this->tags));
    }

    /**
     * @param string $tags
     * @return array
     */
    protected function getHashtags(string $tags): array
    {
        return explode(' ', $tags);
    }
}
