<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemVoucherTest extends TestCase
{
    use RefreshDatabase;

    protected $method = 'POST';

    protected $uri = '/api/sms/relay';

    protected $role = 'spokesman';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function sender_was_a_subscriber_becomes_an_officer()
    {
        /*** arrange ***/
        $from = $this->getRandomMobile(); $to = $this->getRandomMobile(); $message = $this->getVoucherTemplateMessage($this->role);

        /*** assert ***/
        $this->assertNull(Contact::bearing($from));

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::bearing($from), function ($contact) {
            $this->assertFalse($contact->hasRole('subscriber'));
            $this->assertTrue($contact->hasRole($this->role));
        });
    }
}
