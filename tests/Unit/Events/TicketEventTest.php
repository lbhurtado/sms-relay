<?php

namespace Tests\Unit\Events;

use Mockery;
use App\Ticket;
use App\Contact;
use Tests\TestCase;
use App\Events\TicketEvent;
use App\Notifications\Supported;
use App\Listeners\TicketEventSubscriber;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    public function ticket_event_dispatches_supported_notification()
    {
        /*** arrange ***/
        Notification::fake();
        $this->event->shouldReceive('getTicket')->once()->andReturn($ticket = $this->getTicket());

        /*** act ***/
        (new TicketEventSubscriber)->onTicketOpened($this->event);

        /*** assert ***/
        Notification::assertSentTo($ticket->contact, Supported::class);
    }

    protected function getTicket()
    {
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);

        return Ticket::open($contact, $this->faker->sentence);
    }
}

