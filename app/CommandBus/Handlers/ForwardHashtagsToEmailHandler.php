<?php

namespace App\CommandBus\Handlers;

use App\Contact;
use Twitter\Text\Extractor;
use Illuminate\Support\Arr;
use App\Notifications\MailHashtags;
use Illuminate\Support\Facades\Notification;
use App\CommandBus\Commands\ForwardHashtagsToEmailCommand;

class ForwardHashtagsToEmailHandler
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
     * @param ForwardHashtagsToEmailCommand $command
     */
    public function handle(ForwardHashtagsToEmailCommand $command)
    {
        foreach ($this->getHashtags($command) as $hashtag) {
            optional($this->getContacts($hashtag), function ($contacts) use ($command) {
                Notification::send($contacts, new MailHashtags($command->sms));
            });
        };
    }

    /**
     * @param ForwardHashtagsToEmailCommand $command
     * @return array
     */
    protected function getHashtags(ForwardHashtagsToEmailCommand $command): array
    {
        $extracted = $this->extractor->extract($command->sms->getMessage());

        return Arr::get($extracted, 'hashtags');
    }

    /**
     * @param string $hashtag
     * @return mixed
     */
    protected function getContacts(string $hashtag)
    {
        return Contact::whereHas('hashtags', function ($query) use ($hashtag) {
            $query->where('tag', $hashtag);
        })->get();
    }
}
