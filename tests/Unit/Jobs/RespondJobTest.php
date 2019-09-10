<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Respond;
use App\{
    Classes\SupportStage, Contact, Ticket
};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $supporter = factory(Contact::class)->create(['mobile' => '09187654321']);
        $subscriber = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($subscriber, $message);

        /*** act ***/
        $job = new Respond($supporter, $ticket->ticket_id, $message);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(SupportStage::HANDLED, $ticket->status);
    }

    /** @test */
    public function respond_job_on_non_existent_ticket_does_not_update_a_ticket()
    {
        /*** arrange ***/
        $supporter = factory(Contact::class)->create(['mobile' => '09187654321']);
        $subscriber = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($subscriber, $message);

        /*** assert ***/
        $this->expectException(ModelNotFoundException::class);

        /*** act ***/
        $job = new Respond($supporter, $this->faker->word, $message);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(SupportStage::PENDING, $ticket->status);
    }
}
