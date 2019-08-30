<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Relay;
use LBHurtado\Missive\Missive;
use App\CommandBus\RelayAction;
use LBHurtado\Missive\Models\SMS;
use LBHurtado\Missive\Routing\Router;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\{Acknowledged, Hashtags, Forwarded, Relayed};

class RelayActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        $this->artisan('db:seed', ['--class' => 'ContactSeeder']);
    }

    /** @test */
    public function subscriber_relay_action_dispatches_relayed_notifications()
    {
        /*** arrange ***/
        Notification::fake();
        $spokesman = Contact::create(['mobile' => '09654444444'])
            ->syncRoles('spokesman')
            ->setEmail('spokesman@lgu.gov.ph')
            ->catch(['tag1'])
        ;
        $listener1 = Contact::create(['mobile' => '09108888888'])
            ->syncRoles('listener')
            ->setEmail('lister1@@lgu.gov.ph')
            ->catch(['tag1', 'tag3'])
        ;
        $listener2 = Contact::create(['mobile' => '09209999999'])
            ->syncRoles('listener')
            ->setEmail('listener2@lgu.gov.ph')
            ->catch(['tag1'])
        ;
        $listener3 = Contact::create(['mobile' => '09307777777'])
            ->syncRoles('listener')
            ->setEmail('listener3@lgu.gov.ph')
            ->catch(['tag2'])
        ;
        $listener4 = Contact::create(['mobile' => '09456666666'])
            ->syncRoles('listener')
            ->setEmail('listener4@lgu.gov.ph')
            ->catch(['tag3'])
        ;
        $listener4->catch(['tag2']);
        $listener5 = Contact::create(['mobile' => '09555555555'])
            ->syncRoles('listener')
            ->setEmail('listener5@lgu.gov.ph')
            ->catch(['tag3'])
        ;

        $message = "#tag1 #tag2 testing";
        $sms = $this->prepareToRelayAs('subscriber', $message);

        /*** act ***/
        app(RelayAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        Notification::assertSentTo(Contact::bearing($sms->origin->mobile), Acknowledged::class);
        Notification::assertSentTo($spokesman, Hashtags::class);
        Notification::assertSentTo($listener1, Hashtags::class);
        Notification::assertSentTo($listener2, Hashtags::class);
        Notification::assertSentTo($listener3, Hashtags::class);
        Notification::assertSentTo($listener4, Hashtags::class);
        Notification::assertNotSentTo($listener5, Hashtags::class);
        $mobiles = Setting::get('forwarding.mobiles');
        foreach ($mobiles as $mobile) {
            $contact = Contact::bearing($mobile);
            Notification::assertSentTo($contact, Forwarded::class);
        }
        Notification::assertSentTo(Contact::bearing($sms->origin->mobile), Relayed::class);
    }

    protected function prepareToRelayAs(string $role, string $message): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from', 'message'));
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
