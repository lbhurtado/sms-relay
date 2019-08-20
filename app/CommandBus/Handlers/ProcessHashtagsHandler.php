<?php

namespace App\CommandBus\Handlers;

use Illuminate\Support\Arr;
use Twitter\Text\Extractor;
use League\Pipeline\Pipeline;
use App\Mail\ForwardSMSToMail;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Mail;
use Akaunting\Setting\Facade as Setting;
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
        foreach ($this->getHashtags($command) as $hashtag) {
            optional($this->getEmails($hashtag), function ($email) use ($command) {
                $this->send($email, $command->sms);
            });
        };       
    }

    protected function getHashtags(ProcessHashtagsCommand $command)
    {
        $extracted = $this->extractor->extract($command->sms->getMessage());

        return Arr::get($extracted, 'hashtags');
    }

    protected function getEmails($hashtag)
    {
        return Arr::get(Setting::get("forwarding.hashtags"), $hashtag);
    }

    protected function send($email, SMS $sms)
    {
        Mail::to($email)
            ->send(new ForwardSMSToMail($sms))
        ;
    }
}
