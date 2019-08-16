<?php

namespace Tests\Feature;

use Tests\TestCase;
use LBHurtado\SMS\Facades\SMS;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SMSTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $mobile = '639173011987';
        $message = 'Testing from 1898';
        $amount = 25;
        $senderId = 'TXTCMDR';

        SMS::channel('engagespark')
            ->from($senderId)
            ->to($mobile)
            ->content($message)
            ->send()
            // ->topup(25)
            ;
    }
}
