<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Contact, Ticket};
use App\Events\TicketEvents;
use App\Classes\SupportStage;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function ticket_needs_contact_title_message_generates_ticket_id_with_initial_pending_status()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::open($contact, $message);

        /*** assert ***/
        $this->assertTrue($ticket->contact->is($contact));
        $this->assertEquals($message, $ticket->message);
        $this->assertEquals($message, $ticket->message);
        tap(Ticket::hashids()->encode($ticket->id, $ticket->contact->id), function ($ticket_id) use ($ticket) {
            $this->assertEquals($ticket_id, $ticket->ticket_id);
        });
        $this->assertEquals(SupportStage::PENDING, $ticket->status);
    }

    /** @test */
    public function generated_ticket_emits_ticket_event_open()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::open($contact, $message);

        /*** assert ***/
        Event::assertDispatched(TicketEvents::OPENED);
    }

    /** @test */
    public function ticket_must_be_closed_in_order_to_open_a_new_one()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::open($contact, $message);

        /*** assert ***/
        Event::assertDispatched(TicketEvents::OPENED);
    }

    /** @test */
    public function updated_ticket_emits_ticket_event_updated()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::open($contact, $message)->setStage(SupportStage::HANDLED(), $contact, $this->faker->sentence);

        /*** assert ***/
        Event::assertDispatched(TicketEvents::UPDATED);
    }

    /** @test */
    public function resolved_ticket_emits_ticket_event_resolved()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::open($contact, $message)->setStage(SupportStage::RESOLVED(), $contact, $this->faker->sentence);

        /*** assert ***/
        Event::assertDispatched(TicketEvents::RESOLVED);
    }
}
