<?php

namespace Tests\Integration;

use App\Role;
use App\Contact;
use Tests\TestCase;
use LBHurtado\Missive\Missive;
use App\Notifications\Feedback;
use App\Notifications\Broadcast;
use LBHurtado\Missive\Models\SMS;
use App\CommandBus\BroadcastAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BroadcastActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var SMS */
    protected $sms;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        //this needs to be assigned to a class variable, scoping issues with service layer
        $this->sms = $this->createBroadcastSMSBySpokesman(); 
    }

    /** @test */
    public function broadcast_action_sends_to_all_contacts_except_self_but_receives_feedback()
    {
        /*** arrange ***/
        Notification::fake();
        $this->createSubscribers($count = 2);
        $message = $this->faker->sentence;

        /*** act ***/
        app(BroadcastAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        Contact::whereNotIn('mobile',[$this->sms->origin->mobile])->get()->each(function ($contact) use ($message, &$i) {
            ++$i;
            Notification::assertSentTo($contact, Broadcast::class, function ($notification) use ($contact, $message) {
                return Broadcast::getFormattedMessage($contact, $message) == $notification->getContent($contact);
            });
        });
        $this->assertEquals($i, $count);
        tap(Contact::bearing($this->sms->origin->mobile), function ($spokesman) use ($message) {
            Notification::assertSentTo($spokesman, Feedback::class, function ($notification) use ($spokesman, $message) {
                return Feedback::getFormattedMessage($spokesman, $message) == $notification->getContent($spokesman);
            });
        });
    }

    protected function createBroadcastSMSBySpokesman(): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createSpokesman($from);

        $missive = app(Missive::class)->setSMS($sms);  
        (new Router($missive))->process($sms);      

        return $sms;
    }

    protected function createSpokesman(string $mobile)
    {
        $this->spokesman = factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles('spokesman')
        ;

        return $this;
    }

    protected function createSubscribers(int $count)
    {
        for ($i=0; $i < $count; $i++) { 
            $mobile = '0918123456'. $i;
            factory(Contact::class)->create(compact('mobile'));
        }

        return $this;
    }
}
