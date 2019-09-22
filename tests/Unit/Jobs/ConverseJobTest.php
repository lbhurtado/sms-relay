<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Converse;
use App\{
    Classes\SupportStage, Contact, SMSTicket, Ticket
};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConverseJobTest extends TestCase
{
    use RefreshDatabase;

    /** @var Ticket */
    protected $ticket;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $this->ticket = Ticket::open($contact, $message);
    }

    /** @test */
    public function converse_job_converses()
    {
        /*** assert ***/
        $this->assertEquals(1, Ticket::count());
        $this->assertEquals(SupportStage::PENDING, $this->ticket->status);

        /*** arrange ***/
        $contact = $this->ticket->contact;
        $ticket_id = $this->ticket->ticket_id;
        $message = $this->faker->sentence;

        /*** act ***/
        $job = new Converse($contact, $ticket_id, $message);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(1, Ticket::count());
        $this->assertEquals(SupportStage::CONVERSED, $this->ticket->status);
        //TODO: mock Approach Action or something
    }
}
