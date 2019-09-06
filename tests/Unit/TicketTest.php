<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\{Contact, Ticket};
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
    public function ticket_needs_contact_title_message_generates_ticket_id_with_initial_open_status()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $ticket = Ticket::generate($contact, $message);

        /*** assert ***/
        $this->assertTrue($ticket->contact->is($contact));
        $this->assertEquals($message, $ticket->message);
        $this->assertEquals($message, $ticket->message);
        tap(Ticket::getHasher()->encode($ticket->id, $ticket->contact->id), function ($ticket_id) use ($ticket) {
            $this->assertEquals($ticket_id, $ticket->ticket_id);
        });
        $this->assertEquals('open', $ticket->status);
    }
}
