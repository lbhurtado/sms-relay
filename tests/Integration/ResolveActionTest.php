<?php

namespace Tests\Integration;

use TypeError;
use Tests\TestCase;
use App\Jobs\Resolve;
use App\{Contact, Ticket};
use LBHurtado\Missive\Missive;
use App\CommandBus\ResolveAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResolveActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function supporter_resolve_action_dispatches_resolve_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToResolveAs('supporter');
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($contact, $message);
        $ticket_id = $ticket->ticket_id;

        /*** act ***/
        app(ResolveAction::class)('RESOLVE', compact('ticket_id', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Resolve::class, function ($job) use ($sms, $ticket_id, $message) {
            return $job->origin === $sms->origin && $job->ticket_id == $ticket_id && $job->message == $message;
        });
    }

    /** @test */
    public function subscriber_resolve_action_does_not_dispatch_resolve_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToResolveAs('subscriber');
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($contact, $message);
        $ticket_id = $ticket->ticket_id;
        $this->expectException(TypeError::class);

        /*** act ***/
        app(ResolveAction::class)('RESOLVE', compact('ticket_id', 'message'));

        /*** assert ***/
        Bus::assertNotDispatched(Resolve::class);
    }

    protected function prepareToResolveAs(string $role): \LBHurtado\Missive\Models\SMS
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
