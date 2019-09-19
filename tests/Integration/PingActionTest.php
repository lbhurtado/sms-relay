<?php

namespace Tests\Integration;

use TypeError;
use App\Contact;
use Tests\TestCase;
use App\Notifications\Pinged;
use LBHurtado\Missive\Missive;
use App\CommandBus\PingAction;
use LBHurtado\Missive\Models\SMS;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PingActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function spokesman_ping_action_receives_pong()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToPingAs('spokesman');

        /*** act ***/
        app(PingAction::class)->__invoke('', []);

        /*** assert ***/
        Notification::assertSentTo($sms->origin, Pinged::class);
    }

    /** @test */
    public function listener_ping_action_receives_pong()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToPingAs('listener');

        /*** act ***/
        app(PingAction::class)();

        /*** assert ***/
        Notification::assertSentTo($sms->origin, Pinged::class);
    }

    /** @test */
    public function forwarder_ping_action_receives_pong()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToPingAs('forwarder');

        /*** act ***/
        app(PingAction::class)();

        /*** assert ***/
        Notification::assertSentTo($sms->origin, Pinged::class);
    }

    /** @test */
    public function subscriber_ping_action_does_not_receive_a_pong()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToPingAs('subscriber');
        $this->expectException(TypeError::class);

        /*** act ***/
        app(PingAction::class)();

        /*** assert ***/
        Notification::assertNotSentTo($sms->origin, Pinged::class);
    }

    protected function prepareToPingAs(string $role): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($from, $role);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createContact(string $mobile, string $role)
    {
        factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles($role)
        ;

        return $this;
    }
}
