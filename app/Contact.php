<?php

namespace App;

use App\Traits\HasEmail;
use Illuminate\Support\Arr;
use LBHurtado\Missive\Models\SMS;
use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;
use App\Traits\{CanRedeemVouchers, CanSegregateHashtags};
use Illuminatech\Balance\Facades\Balance;

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles, CanRedeemVouchers, HasEmail, CanSegregateHashtags;

    protected $guard_name = 'web';

    protected $appends = array('email');

    /**
     * @param $mobile
     * @return Contact|null
     */
    public static function bearing($mobile):?Contact //TODO change this to by
    {
        return static::whereMobile(phone($mobile, 'PH')
            ->formatE164())
            ->first()
            ;
    }

    /**
     * @param $value
     * @return Contact
     */
    public function setMobileAttribute($value): Contact
    {
        Arr::set($this->attributes, 'mobile', phone($value, 'PH')
            ->formatE164())
        ;

        return $this;
    }

    /**
     * @param string $code
     * @return \BeyondCode\Vouchers\Models\Voucher
     */
    public function redeem(string $code): \BeyondCode\Vouchers\Models\Voucher
    {
        return $this->redeemCode($code);
    }

    /**
     * @param mixed ...$hashtags
     * @return Contact
     */
    public function catch(...$hashtags): Contact
    {
        $hashtags = Arr::flatten($hashtags);

        return $this->catchHashtags($hashtags);
    }

    /**
     * @param array $hashtags
     * @return Contact
     */
    public function uncatch(array $hashtags): Contact
    {
        return $this->uncatchHashtags($hashtags);
    }

    public function smss()
    {
        return $this->hasMany(SMS::class, 'from', 'mobile');
    }

    public function scopeWithinHashtags($query, ...$hashtags)
    {
        $hashtags = Arr::flatten($hashtags);

        return $query->whereHas('hashtags', function ($q) use ($hashtags) {
            return $q->whereIn('tag', $hashtags);
        });
    }

    public function scopeWithHashtag($query, $hashtag)
    {
        return $query->whereHas('hashtags', function ($q) use ($hashtag) {
            return $q->where('tag', $hashtag);
        });
    }

    public function scopeNotBearing($query, ...$mobiles)
    {
        $mobiles = Arr::flatten($mobiles);

        return $query->whereNotIn('mobile', $mobiles);
    }

    public function increase(int $amount)
    {
        Balance::increase(
            [
                'contact_id' => $this->id,
                'type' => 'sms-credits',
            ],
            $amount,
            [
                'org_id' => config('engagespark.org_id'),
            ]
        );

        return $this;
    }

    public function getBalanceAttribute()
    {
        return Balance::calculateBalance($this->id);
    }
}
