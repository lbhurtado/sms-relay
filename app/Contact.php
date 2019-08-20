<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;

class Contact extends BaseContact
{
    use HasEngageSpark, HasRoles;

    protected $guard_name = 'web';

    public static function bearing($mobile) 
    {
    	return static::where('mobile', $mobile)->first();
    }
}
