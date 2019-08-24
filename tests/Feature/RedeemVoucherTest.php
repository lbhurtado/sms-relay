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
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function sender_was_a_subscriber_becomes_an_spokesman()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->getVoucherTemplateMessage('spokesman');

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

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
