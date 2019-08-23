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
    }

    /** @test */
    public function spokesman_sends_a_ping_receives_a_pong()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->keyword;
        $contact = factory(Contact::class)
            ->create(['mobile' => $from])
            ->syncRoles('spokesman');

        /*** act ***/
        Notification::fake();
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertSentTo($contact, Pong::class);
    }

    /** @test */
    public function listener_sends_a_ping_receives_a_pong()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->keyword;
        $contact = factory(Contact::class)
            ->create(['mobile' => $from])
            ->syncRoles('listener');

        /*** act ***/
        Notification::fake();
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertSentTo($contact, Pong::class);
    }

    /** @test */
    public function subscriber_sends_a_ping_does_not_receive_a_pong()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = $this->keyword;
        $contact = factory(Contact::class)
            ->create(['mobile' => $from])
            ->syncRoles('subscriber');

        /*** act ***/
        Notification::fake();
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertNotSentTo($contact, Pong::class);
    }
}
