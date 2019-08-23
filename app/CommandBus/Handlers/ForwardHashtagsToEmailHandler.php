<?php

namespace App\CommandBus\Handlers;

use App\Contact;
use Illuminate\Support\Arr;
use Twitter\Text\Extractor;
use App\Mail\ForwardSMSToMail;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Mail;
use App\CommandBus\Commands\ForwardHashtagsToEmailCommand;
use App\Notifications\MailHashtags;
use Illuminate\Support\Facades\Notification;

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
//            optional($this->getEmails($hashtag), function ($emails) use ($command) {
//                $this->send($emails, $command->sms);
//            });
            optional($this->getContacts($hashtag), function ($contacts) use ($command) {
//                $contacts->notify(new MailHashtags());
                Notification::send($contacts, new MailHashtags($command->sms));
            });
        };
    }

    protected function getHashtags(ForwardHashtagsToEmailCommand $command): array
    {
        $extracted = $this->extractor->extract($command->sms->getMessage());

        return Arr::get($extracted, 'hashtags');
    }

    protected function getEmails(string $hashtag): array
    {
        return Contact::whereHas('hashtags', function ($query) use ($hashtag) {
            $query->where('tag', $hashtag);
        })->get()->pluck('email')->toArray();
    }

    protected function getContacts(string $hashtag)
    {
        return Contact::whereHas('hashtags', function ($query) use ($hashtag) {
            $query->where('tag', $hashtag);
        })->get();
    }

    protected function send(array $emails, SMS $sms)
    {
        $sms->origin->notify(new MailHashtags());
//        //TODO change this to notification
//        Mail::to($emails)
//            ->send(new ForwardSMSToMail($sms)) //TODO change this ForwardHashtagsToEmail
        ;
    }
}
