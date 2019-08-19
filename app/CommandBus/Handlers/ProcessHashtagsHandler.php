<?php

namespace App\CommandBus\Handlers;

use Twitter\Text\Extractor;
use App\CommandBus\Commands\ProcessHashtagsCommand;

class ProcessHashtagsHandler
{
    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * ProcessHashtagsHandler constructor.
     */
    public function __construct()
    {
        $this->extractor = Extractor::create();
    }

    /**
     * @param ProcessHashtagsCommand $command
     */
    public function handle(ProcessHashtagsCommand $command)
    {
        $extracted = $this->extractor->extract($command->message);
    }
}
