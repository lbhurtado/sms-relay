<?php

namespace Tests\Unit\Events;

use Mockery;
use App\Ticket;
use App\Contact;
use Tests\TestCase;
use App\Events\TicketEvent;
use App\Listeners\TicketEventSubscriber;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\{Endorsed, Approach, Responded, Updated, Resolved};

use App\Classes\SupportStage;

class TicketEventTest extends TestCase
{
    use RefreshDatabase;

    /** @var TicketEvent */
    protected $event;

    /** @var Ticket */
    protected $ticket;

    protected $responder;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);

        $this->event = Mockery::mock(TicketEvent::class);
    }

    /** @test */
    public function new_ticket_open_dispatches_approach_and_endorsed_notifications()
    {
        /*** arrange ***/
        Notification::fake();
        factory(Contact::class,2)->create()->each(function ($contact) {
            $contact->syncRoles('supporter');
        });

        $this->event->shouldReceive('listen')->times(6);
        $ticket = $this->getNewTicket();

        /*** act ***/
        (new TicketEventSubscriber)->subscribe($this->event);

        /*** assert ***/
        Notification::assertSentTo($ticket->contact, Endorsed::class);
        Contact::role('supporter')->each(function($supporter) {
            Notification::assertSentTo($supporter, Approach::class);
        });
    }

    /** @test */
    public function ticket_event_handled_dispatches_responded_and_updated_notifications()
    {
        /*** arrange ***/
        Notification::fake();
        $this->event->shouldReceive('listen')->times(6);
        $ticket = $this->getUpdatedTicket($responder = $this->getResponder());

        /*** act ***/
        (new TicketEventSubscriber)->subscribe($this->event);

        /*** assert ***/
        Notification::assertSentTo($ticket->contact, Responded::class);
        Notification::assertSentTo($responder, Updated::class);
    }

    /** @test */
    public function ticket_event_resolved_dispatches_resolved_notifications()
    {
        /*** arrange ***/
        Notification::fake();
        $this->event->shouldReceive('listen')->times(6);
        $ticket = $this->getResolvedTicket($responder = $this->getResponder());

        /*** act ***/
        (new TicketEventSubscriber)->subscribe($this->event);

        /*** assert ***/
        Notification::assertSentTo($responder, Resolved::class);
    }

    protected function getNewTicket()
    {
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);

        return Ticket::open($contact, $this->faker->sentence);
    }

    protected function getUpdatedTicket($responder)
    {
        return $this->getNewTicket()->setStage(SupportStage::HANDLED(), $responder, $this->faker->sentence);
    }

    protected function getResolvedTicket($responder)
    {
        return $this->getNewTicket()->setStage(SupportStage::RESOLVED(), $responder, $this->faker->sentence);
    }

    protected function getResponder()
    {
        return factory(Contact::class)->create(['mobile' => '09187654321']);
    }
}

