<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use App\Notifications\MailHashtags;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelayHashtagsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function hashtags_get_relayed()
    {
        /*** arrange ***/
        Notification::fake();
        $spokesman = Contact::create(['mobile' => '09654444444'])
            ->syncRoles('spokesman')
            ->setEmail('spokesman@lgu.gov.ph')
            ->catch(['tag1'])
        ;
        $listener1 = Contact::create(['mobile' => '09108888888'])
            ->syncRoles('listener')
            ->setEmail('lister1@@lgu.gov.ph')
            ->catch(['tag1', 'tag3'])
        ;
        $listener2 = Contact::create(['mobile' => '09209999999'])
            ->syncRoles('listener')
            ->setEmail('listener2@lgu.gov.ph')
            ->catch(['tag1'])
        ;
        $listener3 = Contact::create(['mobile' => '09307777777'])
            ->syncRoles('listener')
            ->setEmail('listener3@lgu.gov.ph')
            ->catch(['tag2'])
        ;
        $listener4 = Contact::create(['mobile' => '09456666666'])
            ->syncRoles('listener')
            ->setEmail('listener4@lgu.gov.ph')
            ->catch(['tag3'])
        ;
        $listener4->catch(['tag2']);
        $listener5 = Contact::create(['mobile' => '09555555555'])
            ->syncRoles('listener')
            ->setEmail('listener5@lgu.gov.ph')
            ->catch(['tag3'])
        ;

        $from = '09171111111'; $to = '09182222222'; $message = "#tag1 #tag2 testing";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(500);

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertSentTo($spokesman, MailHashtags::class);
        Notification::assertSentTo($listener1, MailHashtags::class);
        Notification::assertSentTo($listener2, MailHashtags::class);
        Notification::assertSentTo($listener3, MailHashtags::class);
        Notification::assertSentTo($listener4, MailHashtags::class);
        Notification::assertNotSentTo($listener5, MailHashtags::class);
    }
}
