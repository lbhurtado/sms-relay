<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Support;
use App\{Contact, Ticket};
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupportJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function support_job_generates_a_ticket()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;

        /*** act ***/
        $job = new Support($contact, $message);
        $job->handle();

        /*** assert ***/
        $ticket = Ticket::first();
        $array = Ticket::hashids()->decode($ticket->ticket_id);
        $this->assertEquals($array[0], $ticket->id);
        $this->assertEquals($array[1], $contact->id);
    }
}