<?php

namespace App\Events;

class SMSRelayEvents
{
    const LISTENED = 'sms-relay.listened';
    const REDEEMED = 'sms-relay.redeemed';
    const RELAYED = 'sms-relay.relayed';
    const UNLISTENED = 'sms-relay.unlistened';
}
