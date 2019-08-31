<?php

namespace App\CommandBus\Handlers;

use App\Contact;
use Twitter\Text\Extractor;
use Illuminate\Support\Arr;
use App\Notifications\Post;
use App\Notifications\Feedback;
use App\CommandBus\Commands\PostCommand;

class PostHandler
{

    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * PostHandler constructor.
     * @param Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param PostCommand $command
     */
    public function handle(PostCommand $command)
    {
        $hashtags = $this->getHashtags($command);
        Contact::notBearing($command->origin->mobile)->withInHashtags($hashtags)->each(function ($contact) use ($command) {
            $contact->notify(new Post($command->message));
        });
        $command->origin->notify(new Feedback($command->message));
    }

    /**
     * @param PostCommand $command
     * @return array
     */
    protected function getHashtags(PostCommand $command): array
    {
        $extracted = $this->extractor->extract($command->sms->getMessage());

        return Arr::get($extracted, 'hashtags');
    }
}
