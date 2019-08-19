<?php

namespace App\CommandBus\Commands;

class ProcessHashtagsCommand
{
    /**
     * @var string
     */
    public $message;

    /**
     * ProcessHashtagsCommand constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
