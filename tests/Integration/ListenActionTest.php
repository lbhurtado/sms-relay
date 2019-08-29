<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Listen;
use LBHurtado\Missive\Missive;
use App\CommandBus\ListenAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var SMS */
    protected $sms;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        //this needs to be assigned to a class variable, scoping issues with service layer
        $this->sms = $this->createListenSMSByListener();
    }

    /** @test */
    public function listen_action()
    {
        /*** arrange ***/
        Bus::fake();
        $tags = $this->faker->sentence;

        /*** act ***/
        app(ListenAction::class)->__invoke('', compact('tags'));

        /*** assert ***/
        Bus::assertDispatched(Listen::class, function ($job) use ($tags) {
            return $job->contact === $this->sms->origin && $job->tags == $tags;
        });
    }

    protected function createListenSMSByListener(): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createListener($from);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createListener(string $mobile)
    {
        factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles('listener')
        ;

        return $this;
    }
}
