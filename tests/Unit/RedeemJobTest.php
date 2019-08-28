<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Redeem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function redeem_code_job_works_for_listener()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $code = $this->getVoucherCode('listener');
        $email = $this->faker->email;

        /*** act ***/
        $job = new Redeem($contact, $code, $email);
        $job->handle();

        /*** assert ***/
        $this->assertFalse ($contact->hasRole('subscriber'));
        $this->assertTrue  ($contact->hasRole('listener'));
        $this->assertEquals($email, $contact->email);
    }

    /** @test */
    public function redeem_code_job_works_for_spokesman()
    {
        /*** arrange ***/
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $code = $this->getVoucherCode('spokesman');
        $email = $this->faker->email;

        /*** act ***/
        $job = new Redeem($contact, $code, $email);
        $job->handle();

        /*** assert ***/
        $this->assertFalse ($contact->hasRole('subscriber'));
        $this->assertTrue  ($contact->hasRole('spokesman'));
        $this->assertEquals($email, $contact->email);
    }
}
