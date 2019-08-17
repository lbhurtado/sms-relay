<?php

namespace App;

use LBHurtado\EngageSpark\Traits\HasEngageSpark;
use LBHurtado\Missive\Models\Contact as BaseContact;

class Contact extends BaseContact
{
    use HasEngageSpark;
}
