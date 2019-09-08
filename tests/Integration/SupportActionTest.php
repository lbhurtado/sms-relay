<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Approach;
use LBHurtado\Missive\Missive;
use App\CommandBus\ApproachAction;
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
    public function subscriber_support_action_dispatches_ticket_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRedeemAs('subscriber');
        $message = $this->faker->sentence;

        /*** act ***/
        app(ApproachAction::class)->__invoke('', compact('title', 'message'));

        /*** assert ***/
        Bus::assertDispatched(Approach::class, function ($job) use ($sms, $message) {
            return $job->origin === $sms->origin && $job->message == $message;
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
