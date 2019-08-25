<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        $this->artisan('db:seed', ['--class' => 'ContactSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function invalid_code_does_make_a_sender_become_a_spokesman_or_listener()
    {
        /*** arrange ***/
        $code = $this->faker->word;
        $email = $this->faker->email;
        $from = '09171111111'; $to = '09182222222'; $message = "{$code} {$email}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(500);

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::bearing($from), function ($contact) {
            $this->assertTrue($contact->hasRole('subscriber'));
            $this->assertFalse($contact->hasRole('listener'));
            $this->assertFalse($contact->hasRole('spokesman'));
        });
    }

    /** @test */
    public function with_valid_code_subscriber_no_more_then_email_from_message_becomes_email_of_sender()
    {
        /*** arrange ***/
        $code = $this->getVoucherCode('listener');
        $email = $this->faker->email;
        $from = '09171111111'; $to = '09182222222'; $message = "{$code} {$email}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(500);

        /*** assert ***/
        $response->assertStatus(200);
        $contact = tap(Contact::bearing($from), function ($contact) {
            $this->assertFalse($contact->hasRole('subscriber'));
            $this->assertTrue($contact->hasRole('listener'));
            $this->assertFalse($contact->hasRole('spokesman'));
        });
        $this->assertEquals($email, $contact->email);
    }

    /** @test */
    public function sender_was_a_subscriber_becomes_an_spokesman()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->getVoucherTemplateMessage('spokesman');

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(500);

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::bearing($from), function ($contact) {
            $this->assertFalse($contact->hasRole('subscriber'));
            $this->assertFalse($contact->hasRole('listener'));
            $this->assertTrue($contact->hasRole('spokesman'));
        });
    }

    /** @test */
    public function sender_was_a_subscriber_becomes_a_listener()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->getVoucherTemplateMessage('listener');

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(500);

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::bearing($from), function ($contact) {
            $this->assertFalse($contact->hasRole('subscriber'));
            $this->assertFalse($contact->hasRole('spokesman'));
            $this->assertTrue($contact->hasRole('listener'));
        });
    }
}
