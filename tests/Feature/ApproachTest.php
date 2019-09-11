<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\ApproachAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApproachTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = '?';

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(ApproachAction::class);
        $this->router->register("{message=(.+)}{$this->keyword}", $this->action);
    }

    /** @test */
    public function message_keyword_invokes_ticket_action()
    {
        /*** arrange ***/
        $message = $this->faker->sentence;
        $from = '09171234567'; $to = '09182222222'; $message = "{$message}{$this->keyword}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function message_alone_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $message = $this->faker->sentence;
        $from = '09171234567'; $to = '09182222222'; $message = "{$message}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }

    /** @test */
    public function keyword_alone_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $from = '09171234567'; $to = '09182222222'; $message =  "{$this->keyword}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
