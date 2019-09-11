<?php

namespace Tests\Integration;

use App\Classes\SupportStage;
use App\Contact;
use App\Ticket;
use Tests\TestCase;
use App\Jobs\Approach;
use LBHurtado\Missive\Missive;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\CommandBus\{ApproachAction, RespondAction};

class ApproachActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function subscriber_approach_action_dispatches_ticket_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToApproachAs('+639191234567', 'subscriber');
        $message = $this->faker->sentence;

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Approach::class, function ($job) use ($sms, $message) {
            return $job->origin === $sms->origin && $job->message == $message;
        });
    }

    /** @test */
    public function subscriber2_approach_action_dispatches_ticket_job()
    {
        /*** arrange ***/
        $sms1 = $this->prepareToApproachAs('+639191234567', 'subscriber');
        $message = $this->faker->sentence;

        /*** assert ***/
        $this->assertEquals(0, Ticket::count());

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        $this->assertEquals(1, Ticket::count());
        $ticket1 = Ticket::first();
        $this->assertTrue($ticket1->smss->first()->is($sms1));
        $this->assertEquals(SupportStage::PENDING, $ticket1->status);

        /*** arrange ***/
        $sms2 = $this->prepareToApproachAs('+639187777777', 'subscriber');
        $message = $this->faker->sentence;

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        $this->assertEquals(2, Ticket::count());
        $ticket2 = Ticket::whereHas('smss', function ($q) use ($sms2) {
            return $q->where('s_m_s_id', $sms2->id);
        })->first();
        $this->assertEquals(SupportStage::PENDING, $ticket2->status);

        /*** arrange ***/
        $sms22 = $this->prepareToApproachAs('+639187777777', 'subscriber');
        $message = $this->faker->sentence;

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        $this->assertEquals(2, Ticket::count());
        $ticket22 = Ticket::whereHas('smss', function ($q) use ($sms22) {
            return $q->where('s_m_s_id', $sms22->id);
        })->first();
        $this->assertTrue($ticket2->is($ticket22));
        $this->assertEquals(SupportStage::PENDING, $ticket22->status);//TODO create a ConverseAction to fix this
    }

    /** @test */
    public function supporter_respond_action_does_this()
    {
        /*** arrange ***/
        $message = $this->faker->sentence;
        $sms1 = $this->prepareToApproachAs('+639191234567', 'subscriber');

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        $ticket = Ticket::first();
        $this->assertTrue($ticket->smss->first()->is($sms1));
        $this->assertEquals(SupportStage::PENDING, $ticket->status);

        /*** arrange ***/
        $ticket_id = $ticket->ticket_id;
        $sms2 = $this->prepareToApproachAs('+639191234567', 'supporter');

        /*** act ***/
        app(RespondAction::class)->__invoke('', compact( 'ticket_id', 'message'));

        /*** assert ***/
        $this->assertTrue(Ticket::find($ticket->id)->smss->where('id', $sms2->id)->first()->is($sms2));
        $this->assertEquals(SupportStage::HANDLED, $ticket->status); //TODO: must have supporter before getting HANDLED

    }

    protected function prepareToApproachAs(string $from, string $role): SMS
    {
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($from, $role);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createContact(string $mobile, string $role)
    {
        Contact::firstOrCreate(compact('mobile'))->syncRoles($role);

        return $this;
    }
}
