<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use LBHurtado\SMS\Facades\SMS;
use LBHurtado\EngageSpark\Notifications\Adhoc;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SMSTest extends TestCase
{
    use RefreshDatabase;

//    /** @test */
    public function sms_facade_works()
    {
        $mobile = '639173011987';
        $message = 'SMSTest::sms_facade_works';
        $senderId = 'TXTCMDR';

        SMS::channel('engagespark')
            ->from($senderId)
            ->to($mobile)
            ->content($message)
            ->send()
            ;

        $this->assertTrue(true);
    }

//    /** @test */
    public function sms_notification_works()
    {
        $contact = factory(Contact::class)->create(['mobile' => '+639173011987']);
        $contact->notify(new Adhoc("SMSTest::sms_notification_works"));

        $this->assertTrue(true);
    }
}
