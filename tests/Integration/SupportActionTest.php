<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Support;
use LBHurtado\Missive\Missive;
use App\CommandBus\SupportAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupportActionTest extends TestCase
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
        $message = $this->faker->sentence;

        /*** act ***/
        app(SupportAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Support::class, function ($job) use ($sms, $message) {
            return $job->contact === $sms->origin && $job->message == $message;
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
