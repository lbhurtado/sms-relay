<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Redeem;
use App\Events\SMSRelayEvents;
use Illuminate\Support\Facades\Event;
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
    public function redeem_code_job_subscriber_becomes_listener_assumes_email_address_sends_redeem_event()
    {
        /*** arrange ***/
        Event::fake();
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
        Event::assertDispatched(SMSRelayEvents::REDEEMED);
    }

    /** @test */
    public function redeem_code_job_subscriber_becomes_spokesman_assumes_email_address_sends_redeem_event()
    {
        /*** arrange ***/
        Event::fake();
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
        Event::assertDispatched(SMSRelayEvents::REDEEMED);
    }

    /** @test */
    public function redeem_code_job_subscriber_becomes_forwarder_assumes_email_address_sends_redeem_event()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $code = $this->getVoucherCode('forwarder');
        $email = $this->faker->email;

        /*** act ***/
        $job = new Redeem($contact, $code, $email);
        $job->handle();

        /*** assert ***/
        $this->assertFalse ($contact->hasRole('subscriber'));
        $this->assertTrue  ($contact->hasRole('forwarder'));
        $this->assertEquals($email, $contact->email);
        Event::assertDispatched(SMSRelayEvents::REDEEMED);
    }

    /** @test */
    public function invalid_redeem_code_job_subscriber_becomes_nothing()
    {
        /*** arrange ***/
        Event::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $code = $this->faker->word;
        $email = $this->faker->email;

        /*** assert ***/
        $this->expectException(\BeyondCode\Vouchers\Exceptions\VoucherIsInvalid::class);

        /*** act ***/
        $job = new Redeem($contact, $code, $email);
        $job->handle();

        /*** assert ***/
        Event::assertNotDispatched(SMSRelayEvents::REDEEMED);
    }
}
