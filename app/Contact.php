<?php

namespace App;

use App\Traits\HasEmail;
use Illuminate\Support\Arr;
use App\Traits\CanRedeemVouchers;
use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;
use App\Events\{SMSRelayEvents, SMSRelayEvent};

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles, CanRedeemVouchers, HasEmail;

    protected $guard_name = 'web';

    public static function bearing($mobile)
    {
        return static::whereMobile(phone($mobile, 'PH')
            ->formatE164())
            ->first()
            ;
    }

    public function setMobileAttribute($value)
    {
        Arr::set($this->attributes, 'mobile', phone($value, 'PH')
            ->formatE164())
        ;
    }

    public function hashtags()
    {
        return $this->hasMany(Hashtag::class);
    }

    public function catch(array $hashtags)
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
