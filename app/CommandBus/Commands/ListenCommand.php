<?php

namespace App\CommandBus\Commands;

use App\Contact;

class ListenCommand
{
    /** @var Contact */
    public $origin;

    /** @var string */
    public $tags;

    /**
     * ListenCommand constructor.
     * @param Contact $origin
     * @param string $tags i.e. space delimited e.g. word1 word2 word3
     */
    public function __construct(Contact $origin, string $tags)
    {
        $this->origin = $origin;
        $this->tags = $tags;
    }
}
