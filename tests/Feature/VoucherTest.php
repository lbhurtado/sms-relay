<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\VoucherAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'VOUCHER';

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(VoucherAction::class);
        $this->router->register("{$this->keyword} {pin}", $this->action);
    }

    /** @test */
    public function voucher_keyword_with_pin_invokes_voucher_action()
    {
        /*** arrange ***/
        $pin = $this->faker->numberBetween(1000,9999);
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->keyword} {$pin}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function none_voucher_keyword_with_pin_do_not_invoke_voucher_action()
    {
        /*** arrange ***/
        $pin = $this->faker->numberBetween(1000,9999);
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->faker->word} {$pin}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
