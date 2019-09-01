<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Credit;
use App\Notifications\Credited;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreditJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function credit_job_works()
    {
        /*** arrange ***/
        Notification::fake();
        $contact = factory(Contact::class)
            ->create(['mobile' => '09171234567'])
        ;
        $amount = $this->faker->numberBetween(10,100);

        /*** act ***/
        $job = new Credit($contact, $amount);
        $job->handle();

        /*** assert ***/
        $this->assertEquals($amount, $contact->balance);
        Notification::assertSentTo($contact, Credited::class);
    }
}
