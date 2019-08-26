<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use App\Notifications\Broadcast;
use App\Notifications\BroadcastFeedback;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'BROADCAST';

    /** @var Contact */
    protected $spokesman, $listener, $subscriber1, $subscriber2, $subscriber3;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->spokesman = factory(Contact::class)->create(['mobile' => '09654444444'])
            ->syncRoles('spokesman')
            ->setEmail('spokesman@lgu.gov.ph')
        ;

        $this->listener = factory(Contact::class)->create(['mobile' => '09108888888'])
            ->syncRoles('listener')
            ->setEmail('lister1@@lgu.gov.ph')
        ;
        $this->subscriber1 = factory(Contact::class)->create(['mobile' => '09209999999'])
            ->syncRoles('subscriber')
            ->setEmail('subscriber1@lgu.gov.ph')
        ;

        $this->subscriber2 = factory(Contact::class)->create(['mobile' => '09307777777'])
            ->syncRoles('listener')
            ->setEmail('$subscriber2@lgu.gov.ph')
            ->catch(['tag2'])
        ;
        $this->subscriber3 = factory(Contact::class)->create(['mobile' => '09456666666'])
            ->syncRoles('listener')
            ->setEmail('$subscriber3@lgu.gov.ph')
            ->catch(['tag3'])
        ;
    }

    /** @test */
    public function spokesman_can_broadcast_and_receive_feedback()
    {
        /*** arrange ***/
        Notification::fake();
        $sender = $this->spokesman;
        $from = $sender->mobile; $to = '09182222222'; $message = "{$this->keyword} {$this->faker->sentence}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::all(), function ($contact) {
            Notification::assertSentTo($contact, Broadcast::class);
        });
        Notification::assertSentTo($sender, BroadcastFeedback::class);
        Notification::assertNotSentTo($this->listener, BroadcastFeedback::class);
        Notification::assertNotSentTo($this->subscriber1, BroadcastFeedback::class);
        Notification::assertNotSentTo($this->subscriber2, BroadcastFeedback::class);
        Notification::assertNotSentTo($this->subscriber3, BroadcastFeedback::class);
        //TODO test received feedback
    }

    /** @test */
    public function listener_cannot_broadcast()
    {
        /*** arrange ***/
        Notification::fake();
        $sender = $this->listener;
        $from = $sender->mobile; $to = '09182222222'; $message = "{$this->keyword} {$this->faker->sentence}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::all(), function ($contact) {
            Notification::assertNotSentTo($contact, Broadcast::class);
            Notification::assertNotSentTo($contact, BroadcastFeedback::class);
        });
    }

    /** @test */
    public function subscriber_cannot_broadcast()
    {
        /*** arrange ***/
        Notification::fake();
        $sender = $this->subscriber1;
        $from = $sender->mobile; $to = '09182222222'; $message = "{$this->keyword} {$this->faker->sentence}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::all(), function ($contact) {
            Notification::assertNotSentTo($contact, Broadcast::class);
            Notification::assertNotSentTo($contact, BroadcastFeedback::class);
        });
    }
}
