<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Respond;
use App\{Contact, Ticket};
use Illuminate\Foundation\Testing\RefreshDatabase;

class RespondJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function respond_job_updates_a_ticket()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($contact, $message);

        /*** act ***/
        $job = new Respond($contact, $ticket->ticket_id, $message);
        $job->handle();

        /*** assert ***/
        $this->assertEquals($message, $ticket->latestStatus('update')->reason );
    }
}
