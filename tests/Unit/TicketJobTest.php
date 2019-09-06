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
    public function ticket_job_generates_a_ticket()
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
        $array = Ticketing::getHasher()->decode($ticket->ticket_id);
        $this->assertEquals($array[0], $ticket->id);
        $this->assertEquals($array[1], $contact->id);
    }
}
