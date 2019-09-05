<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\Ticket;
use App\{Contact, Ticket as Ticketing};
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function ticket_job_subscriber_gets_a_ticket()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $title = $this->faker->title;
        $message = $this->faker->sentence;

        /*** act ***/
        $job = new Ticket($contact, $title, $message);
        $job->handle();

        /*** assert ***/
        $ticket = Ticketing::first();
        $this->assertTrue($ticket->contact->is($contact));
        $this->assertEquals($message, $ticket->message);
        $this->assertEquals($title, $ticket->title);
        $this->assertEquals($message, $ticket->message);
        tap(Ticketing::getHasher()->encode($ticket->id, $ticket->contact->id), function ($ticket_id) use ($ticket) {
            $this->assertEquals($ticket_id, $ticket->ticket_id);
        });
    }
}
