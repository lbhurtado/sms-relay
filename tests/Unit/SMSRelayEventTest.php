<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\{SMSRelayEvent, SMSRelayEvents};
use Illuminate\Support\Facades\Event;
use App\Contact;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Credit;
use App\Listeners\SMSRelayEventSubscriber;

class SMSRelayEventTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
        $this->event = Mockery::mock(SMSRelayEvent::class);
    }

    /** @test */
    public function listener_works()
    {
        /*** arrange ***/
        Bus::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567']);
        $code = $this->getVoucherCode('spokesman');
        $voucher = Voucher::whereCode($code)->first();

        $this->event->shouldReceive('getContact')->once()->andReturn($contact);
        $this->event->shouldReceive('getVoucher')->once()->andReturn($voucher);

        /*** act ***/
        $listener = (new SMSRelayEventSubscriber)->onSMSRelayRedeemed($this->event);

        /*** assert ***/
        Bus::assertDispatched(Credit::class);
    }
}
