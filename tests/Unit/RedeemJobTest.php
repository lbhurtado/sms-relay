<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Redeem;
use App\Notifications\Redeemed;
use Illuminate\Support\Facades\Notification;
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
        Notification::fake();
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
        Notification::assertSentTo($contact, Redeemed::class);//TODO fix this in the future
//        Notification::assertSentTo($contact, Redeemed::class, function ($notification) use ($contact, $code) {
//            dd($notification->getContent($code));
//            dd(Redeemed::getFormattedMessage($contact, $code));
//            return Redeemed::getFormattedMessage($contact, $code) == $notification->getContent($code);
//        });
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
