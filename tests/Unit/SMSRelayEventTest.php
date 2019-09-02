<?php

namespace Tests\Unit;

use Mockery;
use App\Contact;
use Tests\TestCase;
use App\Jobs\Credit;
use App\Events\SMSRelayEvent;
use App\Notifications\Redeemed;
use Illuminate\Support\Facades\Bus;
use BeyondCode\Vouchers\Models\Voucher;
use App\Listeners\SMSRelayEventSubscriber;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SMSRelayEventTest extends TestCase
{
    use RefreshDatabase;

    /** @var SMSRelayEvent */
    protected $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
        $this->event = Mockery::mock(SMSRelayEvent::class);
    }

    /** @test */
    public function redeemed_event_dispatches_credit_job_and_redeemed_notification()
    {
        /*** arrange ***/
        Bus::fake();
        Notification::fake();
        $this->event->shouldReceive('getContact')->once()->andReturn($contact = $this->getContact());
        $this->event->shouldReceive('getVoucher')->once()->andReturn($this->getVoucher());

        /*** act ***/
        (new SMSRelayEventSubscriber)->onSMSRelayRedeemed($this->event);

        /*** assert ***/
        Bus::assertDispatched(Credit::class, function ($job) use ($contact) {
            return $job->contact->is($contact) && config('sms-relay.credits.initial.spokesman') == $job->amount;
        });
        Notification::assertSentTo($contact, Redeemed::class);
    }

    protected function getContact()
    {
        return factory(Contact::class)->create(['mobile' => '09171234567']);
    }

    protected function getVoucher()
    {
        $code = $this->getVoucherCode('spokesman');

        return Voucher::whereCode($code)->first();
    }
}
