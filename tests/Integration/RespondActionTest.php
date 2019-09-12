<?php

namespace Tests\Integration;


use Tests\TestCase;
use App\Jobs\Respond;
use App\{Contact, Ticket};
use App\Classes\SupportStage;
use LBHurtado\Missive\Missive;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\CommandBus\{ApproachAction, RespondAction};

class RespondActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function supporter_respond_action_dispatches_response_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRespondAs('supporter');
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

    /** @test */
    public function subscriber_respond_action_does_not_dispatch_response_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRespondAs('subscriber');
        $contact = factory(Contact::class)->create();
        $message = $this->faker->sentence;
        $ticket = Ticket::open($contact, $message);
        $ticket_id = $ticket->ticket_id;

        /*** act ***/
        app(RespondAction::class)->__invoke('', compact('ticket_id', 'message'));

        /*** assert ***/
        Bus::assertNotDispatched(Respond::class);
    }

    /** @test */
    public function supporter_respond_action_sets_status_to_handled()
    {
        /*** arrange ***/
        $message = $this->faker->sentence;
        $sms1 = $this->prepareToActAs('subscriber');

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        $ticket = Ticket::first();
        $this->assertTrue($ticket->smss->first()->is($sms1));
        $this->assertEquals(SupportStage::PENDING, $ticket->status);

        /*** arrange ***/
        $ticket_id = $ticket->ticket_id;
        $sms2 = $this->prepareToActAs('supporter');

        /*** act ***/
        app(RespondAction::class)->__invoke('', compact( 'ticket_id', 'message'));

        /*** assert ***/
        $this->assertTrue(Ticket::find($ticket->id)->smss->where('id', $sms2->id)->first()->is($sms2));
        $this->assertEquals(SupportStage::HANDLED, $ticket->status); //TODO: must have supporter before getting HANDLED

    }

    protected function prepareToActAs(string $role): SMS
    {
        $from = $this->getRandomMobile();
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($role, $from);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function prepareToRespondAs(string $role): \LBHurtado\Missive\Models\SMS
    {
        $from = $this->getRandomMobile();
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($role, $from);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createContact(string $role, string $mobile)
    {
        factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles($role)
        ;

        return $this;
    }
}
