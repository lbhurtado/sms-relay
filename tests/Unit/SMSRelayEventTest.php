<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\{SMSRelayEvent, SMSRelayEvents};
use Illuminate\Support\Facades\Event;
use App\Contact;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Credit;

class SMSRelayEventTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'VoucherSeeder']);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        /*** arrange ***/
        Bus::fake();
        $contact = factory(Contact::class)->create(['mobile' => '09171234567'])->setEmail($this->faker->email);
        $code = $this->getVoucherCode('spokesman');

        /*** act ***/
        event(SMSRelayEvents::REDEEMED, (new SMSRelayEvent($contact))->setVoucher(Voucher::where('code', $code)->first()));

        /*** assert ***/
        $this->assertTrue(true);
        Bus::assertDispatched(Credit::class);
    }
}
