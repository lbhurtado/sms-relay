<?php

namespace App;

use App\Traits\HasEmail;
use Illuminate\Support\Arr;
use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;
use App\Traits\{CanRedeemVouchers, CanSegregateHashtags};

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles, CanRedeemVouchers, HasEmail, CanSegregateHashtags;

    protected $guard_name = 'web';

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
}
