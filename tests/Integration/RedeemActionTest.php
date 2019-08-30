<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Redeem;
use LBHurtado\Missive\Missive;
use App\CommandBus\RedeemAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function subscriber_redeem_action_dispatches_redeem_job()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToRedeemAs('subscriber');
        $code = $this->faker->word;
        $email = $this->faker->email;

        /*** act ***/
        app(RedeemAction::class)->__invoke('', compact('code', 'email'));

        /*** assert ***/
        Bus::assertDispatched(Redeem::class, function ($job) use ($sms, $code, $email) {
            return $job->contact === $sms->origin && $job->code == $code && $job->email == $email;
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
