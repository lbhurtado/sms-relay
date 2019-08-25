<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\RedeemCodeJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemCodeJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function redeem_code_job_works()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09108888888']);
        $code = $this->getVoucherCode('listener');
        $email = $this->faker->email;

        /*** act ***/
        $job = new RedeemCodeJob($contact, $code, $email);
        $job->handle();

        /*** assert ***/
        $this->assertFalse($contact->hasRole('subscriber'));
        $this->assertTrue($contact->hasRole('listener'));
        $this->assertEquals($email, $contact->email);
    }
}
