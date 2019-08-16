<?php

namespace Tests\Feature;

use Tests\TestCase;
use LBHurtado\SMS\Facades\SMS;

class SMSTest extends TestCase
{
    public function it_works()
    {
        $mobile = '639173011987';
        $message = 'SMSTest::it_works';
        $senderId = 'TXTCMDR';

        SMS::channel('engagespark')
            ->from($senderId)
            ->to($mobile)
            ->content($message)
            ->send()
            ;
    }
}
