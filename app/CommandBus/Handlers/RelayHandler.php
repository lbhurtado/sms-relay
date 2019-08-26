<?php

namespace App\CommandBus\Handlers;

use App\Contact;
use Twitter\Text\Extractor;
use Illuminate\Support\Arr;
use App\Notifications\Hashtags;
use Illuminate\Support\Facades\Notification;
use App\CommandBus\Commands\RelayCommand;

class RelayHandler
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
     * @param RelayCommand $command
     */
    public function handle(RelayCommand $command)
    {
        foreach ($this->getHashtags($command) as $hashtag) {
            optional($this->getContacts($hashtag), function ($contacts) use ($command) {
                Notification::send($contacts, new Hashtags($command->sms));
            });
        };
    }

    /**
     * @param RelayCommand $command
     * @return array
     */
    protected function getHashtags(RelayCommand $command): array
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
