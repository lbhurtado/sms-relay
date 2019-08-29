<?php

namespace App\Traits;

use App\Hashtag;
use App\Events\{SMSRelayEvents, SMSRelayEvent};

trait CanSegregateHashtags
{
    public function hashtags()
    {
        return $this->hasMany(Hashtag::class);
    }

    public function catchHashtags(array $hashtags)
    {
        $tags = [];
        foreach ($hashtags as $tag) {
            $tags[] = ['tag' => $tag];
        }
        $this->hashtags()->createMany($tags);
        event(SMSRelayEvents::LISTENED, (new SMSRelayEvent($this))->setHashtags($hashtags));

        return $this;
    }
}
