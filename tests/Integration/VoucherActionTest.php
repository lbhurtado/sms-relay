<?php

namespace Tests\Integration;

use Setting;
use App\Contact;
use Tests\TestCase;
use App\Notifications\Voucher;
use LBHurtado\Missive\Missive;
use App\CommandBus\VoucherAction;
use LBHurtado\Missive\Models\SMS;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function subscriber_voucher_action_with_correct_pin_receives_vouchers()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToRetrieveVoucherAs('subscriber');
        $pin = Setting::get('PIN');

        /*** act ***/
        app(VoucherAction::class)->__invoke('', compact('pin'));

        /*** assert ***/
        Notification::assertSentTo($sms->origin, Voucher::class);
    }

    /** @test */
    public function subscriber_voucher_action_with_incorrect_pin_does_not_receive_vouchers()
    {
        /*** arrange ***/
        Notification::fake();
        $sms = $this->prepareToRetrieveVoucherAs('subscriber');
        $pin = $this->faker->numberBetween(100, 999);

        /*** act ***/
        app(VoucherAction::class)->__invoke('', compact('pin'));

        /*** assert ***/
        Notification::assertNotSentTo($sms->origin, Voucher::class);
    }

    protected function prepareToRetrieveVoucherAs(string $role): SMS
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
