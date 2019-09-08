<?php

namespace App\Classes;

use Eloquent\Enumeration\AbstractEnumeration;

class SupportStage extends AbstractEnumeration
{
    const OPENED    = 'OPENED';
    const ENDORSED  = 'ENDORSED';
    const PENDING   = 'PENDING';
    const HANDLED   = 'HANDLED';
    const RESOLVED  = 'RESOLVED';
    const CLOSED    = 'CLOSED';
}
