<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use App\Notifications\Pong;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PingTest extends TestCase
{
    use RefreshDatabase;

    protected $method = 'POST';

    protected $uri = '/api/sms/relay';

    protected $keyword = 'PING';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /** @test */
    public function spokesman_sends_a_ping_receives_a_pong()
    {
        /*** arrange ***/
        $from = $this->getRandomMobile(); $to = $this->getRandomMobile(); $message = $this->keyword;
        $contact = factory(Contact::class)->create(['mobile' => $from]);
        $contact->syncRoles('spokesman');
        Notification::fake();

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertSentTo($contact, Pong::class);
    }
}
