<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Ticket;
use LBHurtado\Missive\Missive;
use App\CommandBus\TicketAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function subscriber_ticket_action_dispatches_ticket_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRedeemAs('subscriber');
        $title = $this->faker->title;
        $message = $this->faker->sentence;

        /*** act ***/
        app(TicketAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Ticket::class, function ($job) use ($sms, $title, $message) {
            return $job->contact === $sms->origin && $job->title == $title && $job->message == $message;
        });
    }

    protected function prepareToRedeemAs(string $role): \LBHurtado\Missive\Models\SMS
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
