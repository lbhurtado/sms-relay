<?php

namespace App;

use Illuminate\Support\Arr;
use App\Traits\CanRedeemVouchers;
use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles, CanRedeemVouchers;

    protected $guard_name = 'web';

    public static function bearing($mobile)
    {
    	return static::where('mobile', $mobile)->first();
    }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = phone($value, 'PH')->formatE164();
    }

    public function getEmailAttribute()
    {
        return Arr::get($this->extra_attributes, 'email');
    }

    public function hashtags()
    {
        return $this->hasMany(Hashtag::class);
    }
}
