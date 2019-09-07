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
use App\Notifications\{Supported, Responded, Updated};

class TicketEventTest extends TestCase
{
    use RefreshDatabase;

    /** @var TicketEvent */
    protected $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
        $this->event = Mockery::mock(TicketEvent::class);
    }

    /** @test */
    public function ticket_event_open_dispatches_supported_notification()
    {
        /*** arrange ***/
        Notification::fake();
        $this->event->shouldReceive('getTicket')->once()->andReturn($ticket = $this->getTicket());

        /*** act ***/
        (new TicketEventSubscriber)->onTicketOpened($this->event);

        /*** assert ***/
        Notification::assertSentTo($ticket->contact, Supported::class);
    }

    /** @test */
    public function ticket_event_updated_dispatches_responded_and_updated_notifications()
    {
        /*** arrange ***/
        Notification::fake();
        $this->event->shouldReceive('getTicket')->once()->andReturn($ticket = $this->getUpdatedTicket());
        $this->event->shouldReceive('getResponder')->once()->andReturn($responder = $this->getResponder());

        /*** act ***/
        (new TicketEventSubscriber)->onTicketUpdated($this->event);

        /*** assert ***/
        Notification::assertSentTo($ticket->contact, Responded::class);
        Notification::assertSentTo($responder, Updated::class);
    }

    protected function getTicket()
    {
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);

        return Ticket::open($contact, $this->faker->sentence);
    }

    protected function getUpdatedTicket()
    {
        return $this->getTicket()->setStatus('update', $this->faker->sentence);
    }

    protected function getResponder()
    {
        return factory(Contact::class)->create(['mobile' => '09187654321']);
    }
}

