<?php

namespace Tests\Integration;


use Tests\TestCase;
use App\Jobs\Respond;
use App\{Contact, Ticket};
use LBHurtado\Missive\Missive;
use App\CommandBus\RespondAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RespondActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function listener_respond_action_dispatches_response_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRespondAs('listener');
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $message = $this->faker->sentence;
        $ticket = Ticket::open($contact, $message);
        $ticket_id = $ticket->ticket_id;

        /*** act ***/
        app(RespondAction::class)->__invoke('', compact('ticket_id', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Respond::class, function ($job) use ($sms, $ticket_id, $message) {
            return $job->origin === $sms->origin && $job->ticket_id == $ticket_id && $job->message == $message;
        });
    }

    protected function prepareToRespondAs(string $role): \LBHurtado\Missive\Models\SMS
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
