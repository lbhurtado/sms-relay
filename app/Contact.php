<?php

namespace App;

use App\Traits\HasEmail;
use Illuminate\Support\Arr;
use LBHurtado\Missive\Models\SMS;
use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;
use App\Traits\{CanRedeemVouchers, CanSegregateHashtags};

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles, CanRedeemVouchers, HasEmail, CanSegregateHashtags;

    protected $guard_name = 'web';

    protected $appends = array('email');

    /**
     * @param $mobile
     * @return Contact|null
     */
    public static function bearing($mobile):?Contact
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
     * @param array $hashtags
     * @return Contact
     */
    public function catch(array $hashtags): Contact
    {
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
}
