<?php

namespace App\CommandBus\Handlers;

use Illuminate\Support\Arr;
use Twitter\Text\Extractor;
use League\Pipeline\Pipeline;
use App\CommandBus\Commands\ProcessHashtagsCommand;

class ProcessHashtagsHandler
{
    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * ProcessHashtagsHandler constructor.
     * @param Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param ProcessHashtagsCommand $command
     */
    public function handle(ProcessHashtagsCommand $command)
    {
        $extracted = $this->extractor->extract($command->message);

        dd(implode(",", Arr::get($extracted, 'hashtags')));
        $subject = '';
        $pipeline = (new Pipeline)
            ->pipe(function ($payload) use (&$subject, $extracted) {
                $subject = '#:'.implode(",", Arr::get($extracted, 'hashtags'));
                return $payload;
            })
        ;

        $pipeline->process($command->message);
        dd($subject);
    }
}
