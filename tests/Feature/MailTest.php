<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\SMSForward;
use Illuminate\Support\Facades\Mail;

class MailTest extends TestCase
{
    /** @test */
    public function mail_works()
    {
        $name = 'Krunal';
        Mail::to('lester@3rd.tel')->send(new SMSForward($name));

        $this->assertTrue(true);
    }
}
