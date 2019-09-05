<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\RedeemAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedeemTest extends TestCase
{
    use RefreshDatabase;

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $regex_code = ''; $regex_email = ''; extract(redeem_regex());
        $this->router = app(Router::class);
        $this->action = Mockery::mock(RedeemAction::class);
        $this->router->register("{code={$regex_code}} {email={$regex_email}}", $this->action);
    }

    /** @test */
    public function voucher_space_email_invokes_redeem_action()
    {
        /*** arrange ***/
        $voucher = '2345-6789';
        $email = $this->faker->email;
        $from = '09171234567'; $to = '09182222222'; $message = "{$voucher} {$email}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function invalid_voucher_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $voucher = $this->faker->word;
        $email = $this->faker->email;
        $from = '09171234567'; $to = '09182222222'; $message = "{$voucher} {$email}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }

    /** @test */
    public function invalid_email_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $voucher = '2345-6789';
        $email = $this->faker->word;
        $from = '09171234567'; $to = '09182222222'; $message = "{$voucher} {$email}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }

    /** @test */
    public function voucher_alone_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $voucher = '2345-6789';
        $space = ' ';
        $from = '09171234567'; $to = '09182222222'; $message = "{$voucher}{$space}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
